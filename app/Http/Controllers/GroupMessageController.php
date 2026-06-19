<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    /**
     * Show the group chat interface with all courses.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isTeacher()) {
            // Teacher sees all courses in classrooms they own
            $courses = Course::whereHas('classroom', function($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })->with('classroom.teacher')->get();
        } else {
            // Student sees all courses they have joined
            $courses = $user->courses()->with('classroom.teacher')->get();
        }

        // Fetch last message for each course group chat
        foreach ($courses as $course) {
            $lastMsg = GroupMessage::where('course_id', $course->id)
                ->orderBy('created_at', 'desc')
                ->with('sender')
                ->first();

            $course->last_message = $lastMsg ? $lastMsg->message : '';
            $course->last_message_sender = $lastMsg ? $lastMsg->sender->name : '';
            $course->last_message_sender_id = $lastMsg ? $lastMsg->sender_id : null;
            $course->last_message_time = $lastMsg ? $lastMsg->created_at->diffForHumans() : '';
        }

        return view('groupmes.index', [
            'courses' => $courses
        ]);
    }

    /**
     * Get group message history for a specific course.
     */
    public function getMessages(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Access control check
        if (!$this->hasAccess($user, $course)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền truy cập phòng chat của khóa học này.'
            ], 403);
        }

        // Fetch all group messages
        $messages = GroupMessage::where('course_id', $course->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'sender_role' => $msg->sender->role == 'teacher' ? 'Giáo viên' : 'Học sinh',
                    'message' => e($msg->message),
                    'created_at' => $msg->created_at->format('H:i d/m/Y'),
                    'time_ago' => $msg->created_at->diffForHumans()
                ];
            });

        // Get members list
        $teacher = $course->classroom->teacher;
        $students = $course->students;
        $membersCount = $students->count() + 1; // plus teacher

        return response()->json([
            'status' => 'success',
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'classroom_name' => $course->classroom->name,
                'members_count' => $membersCount,
                'teacher' => [
                    'name' => $teacher->name,
                    'email' => $teacher->email
                ]
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Send a message to a course group chat.
     */
    public function sendMessage(Request $request, Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Access control check
        if (!$this->hasAccess($user, $course)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền gửi tin nhắn vào khóa học này.'
            ], 403);
        }

        $request->validate([
            'message' => 'required|string'
        ]);

        $message = GroupMessage::create([
            'course_id' => $course->id,
            'sender_id' => $user->id,
            'message' => $request->input('message')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $user->name,
                'sender_role' => $user->role == 'teacher' ? 'Giáo viên' : 'Học sinh',
                'message' => e($message->message),
                'created_at' => $message->created_at->format('H:i d/m/Y'),
                'time_ago' => $message->created_at->diffForHumans()
            ]
        ]);
    }

    /**
     * Check if user is teacher or enrolled student of the course.
     */
    private function hasAccess($user, Course $course): bool
    {
        if ($user->isTeacher()) {
            return $course->classroom->teacher_id === $user->id;
        }

        return $course->students()->where('student_id', $user->id)->exists();
    }
}
