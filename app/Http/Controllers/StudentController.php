<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function join()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $approvedClassrooms = $user->classrooms()
            ->wherePivot('status', 'approved')
            ->with('teacher')
            ->get();
        
        $pendingClassrooms = $user->classrooms()
            ->wherePivot('status', 'pending')
            ->with('teacher')
            ->get();

        $availableClassrooms = Classroom::with('teacher')
            ->whereDoesntHave('students', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->get();

        $availableCourses = Course::with(['classroom.teacher'])
            ->whereIn('classroom_id', $approvedClassrooms->pluck('id'))
            ->get();

        $joinedCourseIds = $user->courses()->pluck('course_id')->all();

        return view('student.join', [
            'approvedClassrooms' => $approvedClassrooms,
            'pendingClassrooms' => $pendingClassrooms,
            'availableClassrooms' => $availableClassrooms,
            'availableCourses' => $availableCourses,
            'joinedCourseIds' => $joinedCourseIds,
        ]);
    }

    public function showClassroom(Classroom $classroom)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user is enrolled in this classroom
        $isEnrolled = $user->classrooms()
            ->where('classroom_id', $classroom->id)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Unauthorized');
        }

        $courses = $classroom->courses()->get();
        $joinedCourseIds = $user->courses()->pluck('course_id')->all();

        return view('student.classroom-show', [
            'classroom' => $classroom,
            'courses' => $courses,
            'joinedCourseIds' => $joinedCourseIds,
        ]);
    }

    public function showCourse(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user is enrolled in the classroom of this course
        $isEnrolled = $user->classrooms()
            ->where('classroom_id', $course->classroom_id)
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Unauthorized');
        }

        $rooms = $course->rooms()->get();
        $isJoined = $user->courses()->where('course_id', $course->id)->exists();

        return view('student.course-show', [
            'course' => $course,
            'rooms' => $rooms,
            'isJoined' => $isJoined,
        ]);
    }
}
