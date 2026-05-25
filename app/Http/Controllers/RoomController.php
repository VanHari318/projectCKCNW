<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'join_url' => 'required|url',
            'scheduled_at' => 'required|date',
        ]);

        Room::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'join_url' => $request->join_url,
            'scheduled_at' => $request->scheduled_at,
            'is_active' => true,
        ]);

        return back()->with('success', 'Đã tạo phòng học trực tuyến thành công!');
    }

    public function destroy(Room $room)
    {
        $user = Auth::user();
        $course = $room->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $room->delete();

        return back()->with('success', 'Đã kết thúc và xóa phòng học trực tuyến thành công.');
    }
}
