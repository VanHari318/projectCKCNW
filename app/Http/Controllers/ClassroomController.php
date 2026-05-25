<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function show(Classroom $classroom)
    {
        $user = Auth::user();

        // Authorization checks
        if ($user->isTeacher()) {
            if ($classroom->teacher_id !== $user->id) {
                abort(403, 'Bạn không sở hữu lớp học này.');
            }
        } else {
            // Check if student belongs to this class and is approved
            $enrollment = $classroom->students()->where('student_id', $user->id)->first();
            if (!$enrollment || $enrollment->pivot->status !== 'approved') {
                abort(403, 'Bạn chưa được duyệt vào lớp học này.');
            }
        }

        $classroom->load(['courses', 'teacher']);
        
        $approvedStudents = $classroom->students()
            ->wherePivot('status', 'approved')
            ->get();

        $pendingStudents = collect();
        if ($user->isTeacher()) {
            $pendingStudents = $classroom->students()
                ->wherePivot('status', 'pending')
                ->get();
        }

        return view('classroom.show', [
            'classroom' => $classroom,
            'approvedStudents' => $approvedStudents,
            'pendingStudents' => $pendingStudents,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isTeacher()) {
            abort(403);
        }

        // Check if teacher already has a classroom
        if (Classroom::where('teacher_id', $user->id)->exists()) {
            return back()->with('error', 'Mỗi giáo viên chỉ có thể tạo tối đa 1 lớp học.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Generate unique code
        do {
            $code = strtoupper(Str::random(6));
        } while (Classroom::where('code', $code)->exists());

        Classroom::create([
            'name' => $request->name,
            'code' => $code,
            'teacher_id' => $user->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tạo lớp học thành công! Mã lớp: ' . $code);
    }

    public function destroy(Classroom $classroom)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $classroom->delete();

        return redirect()->route('dashboard')->with('success', 'Đã xóa lớp học thành công.');
    }

    public function join(Request $request)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|string',
        ]);

        $classroom = Classroom::where('code', strtoupper($request->code))->first();

        if (!$classroom) {
            return back()->with('error', 'Không tìm thấy lớp học với mã này.');
        }

        // Check if already requested or joined
        $existing = $classroom->students()->where('student_id', $user->id)->first();
        if ($existing) {
            if ($existing->pivot->status === 'approved') {
                return back()->with('info', 'Bạn đã tham gia lớp học này rồi.');
            } else {
                return back()->with('info', 'Yêu cầu tham gia lớp học đang chờ phê duyệt.');
            }
        }

        $classroom->students()->attach($user->id, ['status' => 'pending']);

        return back()->with('success', 'Đăng ký tham gia lớp học thành công! Vui lòng chờ giáo viên duyệt.');
    }

    public function leave(Classroom $classroom)
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        // Detach classroom
        $classroom->students()->detach($user->id);

        // Detach all courses of this classroom
        $courseIds = $classroom->courses()->pluck('id');
        $user->courses()->detach($courseIds);

        return redirect()->route('dashboard')->with('success', 'Bạn đã rời khỏi lớp học.');
    }

    public function approveStudent(Classroom $classroom, User $student)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $classroom->students()->updateExistingPivot($student->id, ['status' => 'approved']);

        return back()->with('success', "Đã duyệt học sinh {$student->name} vào lớp.");
    }

    public function removeStudent(Classroom $classroom, User $student)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        // Detach classroom
        $classroom->students()->detach($student->id);

        // Detach courses
        $courseIds = $classroom->courses()->pluck('id');
        $student->courses()->detach($courseIds);

        return back()->with('success', "Đã xóa học sinh {$student->name} khỏi lớp.");
    }

    // View specific student information and grades in a modal/page
    public function viewStudentInfo(Classroom $classroom, User $student)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        // Verify student is in this classroom
        $isMember = $classroom->students()->where('student_id', $student->id)->wherePivot('status', 'approved')->exists();
        if (!$isMember) {
            abort(404, 'Học sinh không thuộc lớp học này.');
        }

        // Load grades for courses in this classroom
        $courseIds = $classroom->courses()->pluck('id');
        $grades = Grade::where('student_id', $student->id)
            ->whereIn('course_id', $courseIds)
            ->with(['course', 'gradeable'])
            ->get();

        // Calculate average quiz score
        $quizGrades = Grade::where('student_id', $student->id)
            ->whereIn('course_id', $courseIds)
            ->where('gradeable_type', 'App\\Models\\Quiz')
            ->whereNotNull('score')
            ->get();
        
        $gpa = $quizGrades->count() > 0 ? round($quizGrades->avg('score'), 2) : 'Chưa có';

        return view('classroom.student_info', [
            'classroom' => $classroom,
            'student' => $student,
            'grades' => $grades,
            'gpa' => $gpa,
        ]);
    }
}
