@extends('layouts.app')

@section('title', 'Chi tiết lớp học')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Classroom Info -->
    <div>
        <a href="{{ route('dashboard') }}" class="text-purple-400 hover:text-purple-300 text-sm mb-4 inline-block">← Quay lại</a>
        <h1 class="text-3xl font-bold text-white mb-2">{{ $classroom->name }}</h1>
        <p class="text-gray-400">Giáo viên: {{ $classroom->teacher->name }}</p>
    </div>

    <!-- Room Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Mã Lớp</p>
            <p class="text-xl font-mono font-bold text-purple-300">{{ $classroom->code }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Số Sinh Viên</p>
            <p class="text-xl font-bold text-green-400">{{ $classroom->students->count() }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Số Khóa Học</p>
            <p class="text-xl font-bold text-blue-400">{{ $courses->count() }}</p>
        </div>
    </div>

    <!-- Courses Section -->
    @if($courses->count() > 0)
        <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-4">Khóa Học Trong Lớp</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($courses as $course)
                    <div class="bg-gray-900 rounded p-4 border border-gray-700 hover:border-purple-600 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-white font-semibold">{{ $course->name }}</p>
                                <p class="text-sm text-gray-400 mt-1">{{ $course->description ?? 'Không có mô tả' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            @if(in_array($course->id, $joinedCourseIds))
                                <span class="text-green-400 text-xs">✓ Đã tham gia</span>
                                <a href="{{ route('student.courses.show', $course) }}" class="text-purple-400 hover:text-purple-300 text-sm">
                                    Xem chi tiết →
                                </a>
                            @else
                                <form method="POST" action="{{ route('student.courses.join', $course) }}" style="display:inline;">
                                    @csrf
                                    <button class="text-green-400 hover:text-green-300 text-sm" type="submit">
                                        Tham gia khóa học
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
            <p class="text-gray-400">Lớp học này chưa có khóa học nào.</p>
        </div>
    @endif

    <!-- Leave Class Section -->
    <div class="flex justify-end">
        <form method="POST" action="{{ route('student.classrooms.leave', $classroom) }}" onsubmit="return confirm('Bạn chắc chắn muốn rời khỏi lớp này?');">
            @csrf
            <button class="text-red-400 hover:text-red-300 text-sm" type="submit">
                Rời khỏi lớp học
            </button>
        </form>
    </div>
</div>
@endsection
