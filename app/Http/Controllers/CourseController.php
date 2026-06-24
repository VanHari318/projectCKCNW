<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        $classroom = $course->classroom;

        // Check classroom membership
        $enrollment = $classroom->students()->where('student_id', $user->id)->first();
        if ($user->isStudent()) {
            if (!$enrollment || $enrollment->pivot->status !== 'approved') {
                abort(403, 'Bạn không thuộc lớp học của khóa học này.');
            }
            $isEnrolledInCourse = $course->students()->where('student_id', $user->id)->exists();
        } else {
            if ($classroom->teacher_id !== $user->id) {
                abort(403, 'Bạn không dạy khóa học này.');
            }
            $isEnrolledInCourse = false;
        }

        $course->load(['rooms', 'assignments', 'quizzes']);

        // Load gradebook
        $gradebook = [];
        $studentGrades = [];
        
        if ($user->isTeacher()) {
            // Teacher sees all students and their scores
            $students = $course->students()->get();
            $assignments = $course->assignments;
            $quizzes = $course->quizzes;

            foreach ($students as $student) {
                $grades = Grade::where('course_id', $course->id)
                    ->where('student_id', $student->id)
                    ->get();
                
                $studentData = [
                    'student' => $student,
                    'grades' => []
                ];

                foreach ($assignments as $assign) {
                    $grade = $grades->where('gradeable_type', 'App\\Models\\Assignment')
                                   ->where('gradeable_id', $assign->id)
                                   ->first();
                    $studentData['grades']['assignment_' . $assign->id] = $grade;
                }

                foreach ($quizzes as $quiz) {
                    $grade = $grades->where('gradeable_type', 'App\\Models\\Quiz')
                                   ->where('gradeable_id', $quiz->id)
                                   ->first();
                    $studentData['grades']['quiz_' . $quiz->id] = $grade;
                }

                // Quiz Average
                $quizGrades = $grades->where('gradeable_type', 'App\\Models\\Quiz')->whereNotNull('score');
                $studentData['quiz_gpa'] = $quizGrades->count() > 0 ? round($quizGrades->avg('score'), 2) : 'Chưa có';

                $gradebook[] = $studentData;
            }
        } else {
            // Student sees only their own scores
            $myGrades = Grade::where('course_id', $course->id)
                ->where('student_id', $user->id)
                ->get();

            foreach ($course->assignments as $assign) {
                $studentGrades['assignment_' . $assign->id] = $myGrades
                    ->where('gradeable_type', 'App\\Models\\Assignment')
                    ->where('gradeable_id', $assign->id)
                    ->first();
            }

            foreach ($course->quizzes as $quiz) {
                $studentGrades['quiz_' . $quiz->id] = $myGrades
                    ->where('gradeable_type', 'App\\Models\\Quiz')
                    ->where('gradeable_id', $quiz->id)
                    ->first();
            }
            
            // Calculate Quiz Average
            $quizGrades = $myGrades->where('gradeable_type', 'App\\Models\\Quiz')->whereNotNull('score');
            $quizGpa = $quizGrades->count() > 0 ? round($quizGrades->avg('score'), 2) : 'Chưa có';
        }

        return view('course.show', [
            'course' => $course,
            'classroom' => $classroom,
            'isEnrolled' => $user->isTeacher() ? true : $isEnrolledInCourse,
            'gradebook' => $gradebook,
            'studentGrades' => $studentGrades,
            'quizGpa' => $user->isStudent() ? ($quizGpa ?? 'Chưa có') : null,
        ]);
    }

    public function store(Request $request, Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'classroom_id' => $classroom->id,
        ]);

        return back()->with('success', 'Tạo khóa học mới thành công!');
    }

    public function destroy(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        $classroom = $course->classroom;
        if (!$user->isTeacher() || $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('teacher.manage')->with('success', 'Xóa khóa học thành công.');
    }

    public function join(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $classroom = $course->classroom;
        $enrollment = $classroom->students()->where('student_id', $user->id)->first();
        if (!$enrollment || $enrollment->pivot->status !== 'approved') {
            abort(403, 'Bạn chưa được duyệt vào lớp học này.');
        }

        // Attach course student
        if (!$course->students()->where('student_id', $user->id)->exists()) {
            $course->students()->attach($user->id);
        }

        return back()->with('success', 'Đã tham gia khóa học.');
    }

    public function leave(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $course->students()->detach($user->id);

        return redirect()->route('student.classrooms.show', $course->classroom_id)->with('success', 'Bạn đã rời khỏi khóa học.');
    }
}
