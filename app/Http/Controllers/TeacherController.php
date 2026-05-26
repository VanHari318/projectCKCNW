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
        
        $classroom = Classroom::with(['courses.rooms', 'students'])
            ->where('teacher_id', $teacher->id)
            ->first();

        $approvedStudents = collect();
        $pendingStudents = collect();

        if ($classroom) {
            $approvedStudents = $classroom->students()
                ->wherePivot('status', 'approved')
                ->get();
            $pendingStudents = $classroom->students()
                ->wherePivot('status', 'pending')
                ->get();
        }

        return view('teacher.manage', [
            'classroom' => $classroom,
            'approvedStudents' => $approvedStudents,
            'pendingStudents' => $pendingStudents,
        ]);
    }
}
