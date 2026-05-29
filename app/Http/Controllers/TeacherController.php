<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
}
