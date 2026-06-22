@extends('layouts.app')

@section('title', __('Bảng Điều Khiển') . ' ' . __('Học Sinh'))

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ __('Bảng Điều Khiển') }} <span class="text-purple-500">{{ __('Học Sinh') }}</span></h1>
            <p class="text-gray-400">{{ __('Xem lớp học, khóa học và điểm số của bạn') }}</p>
        </div>

        <div class="bg-gradient-to-r from-purple-900 to-purple-800 rounded-lg p-8 border border-purple-600">
            <h2 class="text-2xl font-bold text-white mb-2">{{ __('Xin chào,') }} {{ auth()->user()->name }}! 👋</h2>
            <p class="text-purple-100">{{ __('Bạn đang đăng nhập với tư cách') }} <strong>{{ __('Học Sinh') }}</strong></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-green-900 rounded-lg p-6 border border-green-600">
                <p class="text-gray-300 text-sm">{{ __('Lớp (Duyệt)') }}</p>
                <p class="text-3xl font-bold text-green-200">{{ $joinedClassrooms->count() }}</p>
            </div>
            <div class="bg-yellow-900 rounded-lg p-6 border border-yellow-600">
                <p class="text-gray-300 text-sm">{{ __('Chờ Duyệt') }}</p>
                <p class="text-3xl font-bold text-yellow-200">{{ $pendingClassrooms->count() }}</p>
            </div>
            <div class="bg-blue-900 rounded-lg p-6 border border-blue-600">
                <p class="text-gray-300 text-sm">{{ __('Khóa Học') }}</p>
                <p class="text-3xl font-bold text-blue-200">{{ $joinedCourses->count() }}</p>
            </div>
            <div class="bg-pink-900 rounded-lg p-6 border border-pink-600">
                <p class="text-gray-300 text-sm">{{ __('Điểm TB') }}</p>
                <p class="text-3xl font-bold text-pink-200">{{ $averageQuizScore ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 border border-purple-600">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ __('Tham gia lớp & khóa học') }}</h3>
                    <p class="text-gray-300">{{ __('Nhập mã lớp hoặc chọn khóa học phù hợp') }}</p>
                </div>
                <a class="btn-primary px-6 py-2 rounded" href="{{ route('student.join') }}">{{ __('Tham gia lớp học') }}</a>
            </div>
        </div>

        @if ($joinedClassrooms->count() > 0)
            <div>
                <h3 class="text-2xl font-bold text-white mb-4">📚 {{ __('Lớp Học Của Bạn') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($joinedClassrooms as $classroom)
                        <a href="{{ route('student.classrooms.show', $classroom) }}" class="block bg-gray-800 rounded-lg p-6 border border-purple-600 hover:border-purple-400 transition">
                            <h4 class="text-xl font-bold text-white mb-2">{{ $classroom->name }}</h4>
                            <p class="text-gray-400 text-sm mb-3">{{ $classroom->description ?? __('Không có mô tả') }}</p>
                            <p class="text-sm text-gray-300">👨‍🏫 {{ $classroom->teacher->name }}</p>
                            <p class="text-purple-400 text-sm mt-3">{{ __('Xem chi tiết →') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($pendingClassrooms->count() > 0)
            <div>
                <h3 class="text-2xl font-bold text-white mb-4">⏳ {{ __('Yêu Cầu Tham Gia Chờ Duyệt') }}</h3>
                <div class="space-y-3">
                    @foreach ($pendingClassrooms as $classroom)
                        <div class="bg-gray-800 rounded-lg p-4 border border-yellow-600">
                            <h4 class="text-lg font-bold text-white">{{ $classroom->name }}</h4>
                            <p class="text-gray-400 text-sm">👨‍🏫 {{ $classroom->teacher->name }}</p>
                            <p class="text-yellow-400 text-sm">{{ __('Chờ duyệt...') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($joinedCourses->count() > 0)
            <div>
                <h3 class="text-2xl font-bold text-white mb-4">📖 {{ __('Khóa Học') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($joinedCourses as $course)
                        <a href="{{ route('student.courses.show', $course) }}" class="block bg-gray-800 rounded-lg p-6 border border-blue-600 hover:border-blue-400 transition">
                            <h4 class="text-xl font-bold text-white mb-2">{{ $course->name }}</h4>
                            <p class="text-gray-400 text-sm mb-3">{{ $course->description ?? __('Không có mô tả') }}</p>
                            <p class="text-sm text-gray-300">{{ __('Lớp:') }} {{ $course->classroom->name }}</p>
                            <p class="text-blue-400 text-sm mt-3">{{ __('Xem chi tiết →') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
