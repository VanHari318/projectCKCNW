<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isTeacher()) {
            // Teacher Dashboard
            $classroom = Classroom::with(['courses', 'students'])->where('teacher_id', $user->id)->first();
            
            $pendingStudents = collect();
            if ($classroom) {
                $pendingStudents = $classroom->students()->wherePivot('status', 'pending')->get();
            }

            return view('dashboard.teacher', [
                'user' => $user,
                'classroom' => $classroom,
                'pendingStudents' => $pendingStudents,
            ]);
        } else {
            // Student Dashboard
            $joinedClassrooms = $user->classrooms()->wherePivot('status', 'approved')->with('teacher')->get();
            $pendingClassrooms = $user->classrooms()->wherePivot('status', 'pending')->with('teacher')->get();
            $joinedCourses = $user->courses()->with('classroom')->get();
            
            // Calculate Quiz GPA
            $quizGrades = Grade::where('student_id', $user->id)
                ->where('gradeable_type', 'App\\Models\\Quiz')
                ->whereNotNull('score')
                ->get();
            
            $averageQuizScore = $quizGrades->count() > 0 ? round($quizGrades->avg('score'), 2) : null;

            return view('dashboard.student', [
                'user' => $user,
                'joinedClassrooms' => $joinedClassrooms,
                'pendingClassrooms' => $pendingClassrooms,
                'joinedCourses' => $joinedCourses,
                'averageQuizScore' => $averageQuizScore,
            ]);
        }
    }

    public function grades()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            abort(403);
        }

        $grades = Grade::where('student_id', $user->id)
            ->with(['classroom', 'course', 'gradeable'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $quizGrades = $grades->where('gradeable_type', 'App\\Models\\Quiz')->whereNotNull('score');
        $averageQuizScore = $quizGrades->count() > 0 ? round($quizGrades->avg('score'), 2) : 'Chưa có';

        $assignmentGrades = $grades->where('gradeable_type', 'App\\Models\\Assignment')->whereNotNull('score');
        $averageAssignmentScore = $assignmentGrades->count() > 0 ? round($assignmentGrades->avg('score'), 2) : 'Chưa có';

        return view('student.grades', [
            'grades' => $grades,
            'averageQuizScore' => $averageQuizScore,
            'averageAssignmentScore' => $averageAssignmentScore,
        ]);
    }
}
