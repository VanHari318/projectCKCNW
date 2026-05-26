@extends('layouts.app')

@section('title', 'Dashboard Giáo Viên')

@section('content')
    <div class="flex flex-col gap-6">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Bảng Điều Khiển <span class="text-purple-500">Giáo Viên</span></h1>
            <p class="text-gray-400">Quản lý lớp học, học sinh và phòng học trực tuyến</p>
        </div>

        <div class="bg-gradient-to-r from-purple-900 to-purple-800 rounded-lg p-8 border border-purple-600">
            <h2 class="text-2xl font-bold text-white mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-purple-100">Bạn đang đăng nhập với tư cách <strong>Giáo Viên</strong></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-purple-900 rounded-lg p-6 border border-purple-600">
                <p class="text-gray-300 text-sm">Lớp Học</p>
                <p class="text-3xl font-bold text-purple-200">{{ $classroom ? 1 : 0 }}</p>
            </div>
            <div class="bg-purple-900 rounded-lg p-6 border border-purple-600">
                <p class="text-gray-300 text-sm">Học Sinh</p>
                <p class="text-3xl font-bold text-purple-200">{{ $classroom ? $classroom->students()->count() : 0 }}</p>
            </div>
            <div class="bg-orange-900 rounded-lg p-6 border border-orange-600">
                <p class="text-gray-300 text-sm">Chờ Duyệt</p>
                <p class="text-3xl font-bold text-orange-200">{{ $pendingStudents ? $pendingStudents->count() : 0 }}</p>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 border border-purple-600">
            @if ($classroom)
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2">📖 {{ $classroom->name }}</h3>
                        <p class="text-gray-300">Mã lớp: <span class="text-purple-300 font-mono">{{ $classroom->code }}</span></p>
                    </div>
                    <a class="btn-primary px-5 py-2 rounded" href="{{ route('teacher.manage') }}">Quản lý lớp & khóa học</a>
                </div>
            @else
                <div class="text-center">
                    <p class="text-gray-400 mb-4">Bạn chưa tạo lớp học nào</p>
                    <a class="btn-primary px-6 py-2 rounded" href="{{ route('teacher.manage') }}">+ Tạo lớp mới</a>
                </div>
            @endif
        </div>
    </div>
@endsection
