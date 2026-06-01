<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function store(Request $request, Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'room_type' => 'required|in:jitsi,external',
            'join_url' => 'required_if:room_type,external|nullable|url',
            'scheduled_at' => 'required|date',
        ]);

        $joinUrl = $request->join_url;
        if ($request->room_type === 'jitsi') {
            $slug = Str::slug($request->title);
            $randomString = Str::random(10);
            $joinUrl = "https://p2p.mirotalk.com/join/projectck_{$course->id}_{$slug}_{$randomString}";
        }

        $room = Room::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'join_url' => $joinUrl,
            'scheduled_at' => $request->scheduled_at,
            'is_active' => true,
        ]);

        return redirect()->route('rooms.show', $room);
    }

    public function show(Room $room)
    {
        /** @var User $user */
        $user = Auth::user();
        $course = $room->course;
        $classroom = $course->classroom;

        if ($user->isTeacher()) {
            if ($classroom->teacher_id !== $user->id) {
                abort(403);
            }
        } else {
            $isApproved = $classroom->students()
                ->where('student_id', $user->id)
                ->wherePivot('status', 'approved')
                ->exists();

            if (!$isApproved) {
                abort(403);
            }
        }

        if (Str::contains($room->join_url, 'mirotalk.com') || Str::contains($room->join_url, 'meet.jit.si') || Str::contains($room->join_url, 'meet.ffmuc.net')) {
            return view('classroom.room', compact('room'));
        }

        return redirect()->away($room->join_url);
    }

    public function destroy(Room $room)
    {
        /** @var User $user */
        $user = Auth::user();
        $course = $room->course;
        if (!$user->isTeacher() || $course->classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $room->delete();

        return back()->with('success', 'Đã kết thúc và xóa phòng học trực tuyến thành công.');
    }
}

