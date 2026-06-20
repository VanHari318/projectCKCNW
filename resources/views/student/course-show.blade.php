@extends('layouts.app')

@section('title', 'Chi tiết khóa học')

@section('content')
<div class="flex flex-col gap-8">
    @php
        $courseStudents = $course->students;
    @endphp

    <!-- Course Info -->
    <div>
        <a href="{{ route('student.classrooms.show', $course->classroom) }}" class="text-purple-400 hover:text-purple-300 text-sm mb-4 inline-block">← Quay lại lớp học</a>
        <h1 class="text-3xl font-bold text-white mb-2">{{ $course->name }}</h1>
        <p class="text-gray-400">Lớp: {{ $course->classroom->name }}</p>
        <p class="text-gray-400">Giáo viên: {{ $course->classroom->teacher->name }}</p>
    </div>

    <!-- Course Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Mô Tả</p>
            <p class="text-white mt-2">{{ $course->description ?? 'Không có mô tả' }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Số Phòng Học</p>
            <p class="text-2xl font-bold text-blue-400">{{ $rooms->count() }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Số Học Sinh Trong Khóa</p>
            <p class="text-2xl font-bold text-green-400">{{ $courseStudents->count() }}</p>
        </div>
    </div>

    <!-- Status -->
    @if(!$isJoined)
        <div class="bg-yellow-900 border border-yellow-600 rounded-lg p-6">
            <p class="text-yellow-200 mb-4">Bạn chưa tham gia khóa học này. Hãy tham gia để xem thêm thông tin.</p>
            <form method="POST" action="{{ route('student.courses.join', $course) }}">
                @csrf
                <button class="btn-primary px-6 py-2 rounded" type="submit">
                    Tham gia khóa học
                </button>
            </form>
        </div>
    @else
        <!-- Rooms Section -->
        @if($rooms->count() > 0)
            <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-4">Phòng Học</h2>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($rooms as $room)
                        <div class="bg-gray-900 rounded p-4 border border-gray-700 hover:border-purple-600 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-white font-semibold">{{ $room->title }}</p>
                                    <p class="text-sm text-gray-400 mt-1">
                                        📅 {{ \Carbon\Carbon::parse($room->scheduled_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex gap-3">
                                <a href="{{ route('rooms.show', $room) }}" class="text-purple-400 hover:text-purple-300 text-sm font-semibold">
                                    Vào phòng →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
                <p class="text-gray-400">Khóa học này chưa có phòng học nào được lên lịch.</p>
            </div>
        @endif

        <div class="bg-gray-800 border border-green-600 rounded-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Danh Sách Học Sinh Trong Khóa</h2>
            @if($courseStudents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($courseStudents as $student)
                        <div class="bg-gray-900 rounded p-4 border border-gray-700">
                            <p class="text-white font-semibold">{{ $student->name }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400">Khóa học này chưa có học sinh nào tham gia.</p>
            @endif
        </div>

        <!-- Assignments Section -->
        <div class="bg-gray-800 border border-blue-600 rounded-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Bài Tập</h2>
            @if($assignments->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($assignments as $assignment)
                        @php
                            $gradeKey = 'App\\Models\\Assignment_'.$assignment->id;
                            $grade = $grades->get($gradeKey)?->first();
                            $isOverdue = $assignment->due_date && $assignment->due_date->isPast();
                        @endphp
                        <div class="bg-gray-900 rounded p-4 border border-gray-700 hover:border-blue-500 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-white font-semibold">{{ $assignment->title }}</p>
                                    <p class="text-sm {{ $isOverdue && !$grade ? 'text-red-400' : 'text-gray-400' }} mt-1">
                                        Hạn: {{ $assignment->due_date?->format('d/m/Y H:i') }}
                                        @if($isOverdue && !$grade)
                                            <span class="ml-2 text-xs bg-red-900 text-red-300 px-2 py-0.5 rounded">Đã hết hạn</span>
                                        @endif
                                    </p>
                                </div>
                                @if($grade)
                                    <span class="text-green-400 text-sm">Điểm: {{ $grade->score }}/10</span>
                                @endif
                            </div>
                            <div class="mt-3">
                                @if($grade)
                                    <a href="{{ route('student.assignments.review', $assignment) }}" class="text-purple-300 hover:text-purple-200 text-sm">
                                        Xem bài làm →
                                    </a>
                                @elseif($isOverdue)
                                    <button type="button" onclick="alert('Bài tập đã kết thúc! Bạn không thể làm bài này nữa.')" class="text-red-400 hover:text-red-300 text-sm cursor-pointer">
                                        Bài tập đã kết thúc ✕
                                    </button>
                                @else
                                    <a href="{{ route('student.assignments.take', $assignment) }}" class="text-blue-400 hover:text-blue-300 text-sm">
                                        Làm bài →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400">Khóa học này chưa có bài tập.</p>
            @endif
        </div>

        <!-- Bài kiểm tra -->
        <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Bài Kiểm Tra</h2>
            @if($quizzes->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($quizzes as $quiz)
                        @php
                            $gradeKey = 'App\\Models\\Quiz_'.$quiz->id;
                            $grade = $grades->get($gradeKey)?->first();
                            $isOverdue = $quiz->due_date && $quiz->due_date->isPast();
                        @endphp
                        <div class="bg-gray-900 rounded p-4 border border-gray-700 hover:border-purple-500 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-white font-semibold">{{ $quiz->title }}</p>
                                    <p class="text-sm {{ $isOverdue && !$grade ? 'text-red-400' : 'text-gray-400' }} mt-1">
                                        Hạn: {{ $quiz->due_date?->format('d/m/Y H:i') }}
                                        @if($isOverdue && !$grade)
                                            <span class="ml-2 text-xs bg-red-900 text-red-300 px-2 py-0.5 rounded">Đã hết hạn</span>
                                        @endif
                                    </p>
                                </div>
                                @if($grade)
                                    <span class="text-green-400 text-sm">Điểm: {{ $grade->score }}/10</span>
                                @endif
                            </div>
                            <div class="mt-3">
                                @if($grade)
                                    <a href="{{ route('student.quizzes.review', $quiz) }}" class="text-purple-300 hover:text-purple-200 text-sm">
                                        Xem bài làm →
                                    </a>
                                @elseif($isOverdue)
                                    <button type="button" onclick="alert('Bài kiểm tra đã kết thúc! Bạn không thể làm bài này nữa.')" class="text-red-400 hover:text-red-300 text-sm cursor-pointer">
                                        Bài kiểm tra đã kết thúc ✕
                                    </button>
                                @else
                                    <a href="{{ route('student.quizzes.take', $quiz) }}" class="text-purple-400 hover:text-purple-300 text-sm">
                                        Làm bài →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400">Khóa học này chưa có bài kiểm tra.</p>
            @endif
        </div>

        <!-- Leave Course Section -->
        <div class="flex justify-end">
            <form method="POST" action="{{ route('student.courses.leave', $course) }}" onsubmit="return confirm('Bạn chắc chắn muốn rời khỏi khóa học này?');">
                @csrf
                <button class="text-red-400 hover:text-red-300 text-sm" type="submit">
                    Rời khỏi khóa học
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
