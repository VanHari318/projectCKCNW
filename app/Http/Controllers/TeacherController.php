<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function manage()
    {
        /** @var User $teacher */
        $teacher = Auth::user();
        
        $classroom = Classroom::with([
            'courses.rooms',
            'courses.assignments.grades.student',
            'courses.quizzes.grades.student',
            'students',
        ])
            ->where('teacher_id', $teacher->id)
            ->first();

        $approvedStudents = collect();
        $pendingStudents = collect();

        if ($classroom) {
            $approvedStudents = $classroom->students()
                ->wherePivot('status', 'approved')
                ->orderBy('name')
                ->get();
            $pendingStudents = $classroom->students()
                ->wherePivot('status', 'pending')
                ->orderBy('name')
                ->get();
        }

        return view('teacher.manage', [
            'classroom' => $classroom,
            'approvedStudents' => $approvedStudents,
            'pendingStudents' => $pendingStudents,
        ]);
    }
    /**
     * Hiển thị source bài tập và bài kiểm tra cho teacher hiện tại
     */
    public function sources(Request $request)
    {
        $teacher = Auth::user();

        $courseIds = Course::whereHas('classroom', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->pluck('id');

        //$assignments = Assignment::whereIn('course_id', $courseIds)->get();
        //$quizzes = Quiz::whereIn('course_id', $courseIds)->get();
        // Gộp trùng lặp: chỉ hiện 1 bài duy nhất cho mỗi bộ (title + câu hỏi) giống nhau
        $assignments = Assignment::with('course')->whereIn('course_id', $courseIds)->get()
            ->unique(fn($a) => $a->title . '|' . md5(json_encode($a->questions)));
        $quizzes = Quiz::with('course')->whereIn('course_id', $courseIds)->get()
            ->unique(fn($q) => $q->title . '|' . md5(json_encode($q->questions)));

        $courses = Course::whereIn('id', $courseIds)->get();

        // provide classroom so the repost modal can include classroom_id when needed
        $classroom = Classroom::where('teacher_id', $teacher->id)->first();
        // return view depending on requested route/path
        $routeName = $request->route() ? $request->route()->getName() : null;
        if ($routeName === 'teacher.quizzes.index' || $request->is('teacher/quizzes')) {
            return view('teacher.quizzes.index', compact('assignments', 'quizzes', 'courses', 'classroom'));
        }
        return view('teacher.assignments.index', compact('assignments', 'quizzes', 'courses', 'classroom'));
    }
    // --- add helper to provide assignments/quizzes list to manage view ---
    public function manageWithSources()
    {
        /** @var User $teacher */
        $teacher = Auth::user();

        $classroom = Classroom::with([
            'courses.rooms',
            'courses.assignments.grades.student',
            'courses.quizzes.grades.student',
            'students',
        ])
            ->where('teacher_id', $teacher->id)
            ->first();

        $approvedStudents = collect();
        $pendingStudents = collect();

        if ($classroom) {
            $approvedStudents = $classroom->students()
                ->wherePivot('status', 'approved')
                ->orderBy('name')
                ->get();
            $pendingStudents = $classroom->students()
                ->wherePivot('status', 'pending')
                ->orderBy('name')
                ->get();
        }

        // collect teacher's assignments/quizzes from courses
        $courseIds = $classroom ? $classroom->courses->pluck('id') : collect();
        $teacherAssignments = Assignment::whereIn('course_id', $courseIds)
            ->get(['id', 'title', 'questions']);
        $teacherQuizzes = Quiz::whereIn('course_id', $courseIds)
            ->get(['id', 'title', 'questions']);

        return view('teacher.manage', [
            'classroom' => $classroom,
            'approvedStudents' => $approvedStudents,
            'pendingStudents' => $pendingStudents,
            'teacherAssignments' => $teacherAssignments,
            'teacherQuizzes' => $teacherQuizzes,
        ]);
    }
}
