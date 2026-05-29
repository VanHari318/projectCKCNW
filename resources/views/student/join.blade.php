@extends('layouts.app')

@section('title', 'Tham gia lớp & khóa học')

@section('content')
    <div class="flex flex-col gap-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Tham gia <span class="text-purple-500">Lớp & Khóa học</span></h1>
            <p class="text-gray-400">Nhập mã lớp để tham gia hoặc chọn khóa học từ lớp đã tham gia</p>
        </div>

        <!-- Join Classroom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Tham gia lớp mới</h2>
                <form method="POST" action="{{ route('student.classrooms.join') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-300 mb-2">Nhập mã lớp</label>
                        <input name="code" class="w-full rounded form-input px-4 py-2 uppercase" 
                               placeholder="Ví dụ: ABC123" required maxlength="6">
                    </div>
                    <button class="btn-primary px-4 py-2 rounded w-full" type="submit">
                        Tham gia lớp
                    </button>
                </form>
            </div>

            <!-- Available Classrooms -->
            <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Lớp học khác</h2>
                @if($availableClassrooms->count() > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($availableClassrooms as $classroom)
                            <div class="bg-gray-900 rounded p-3 border border-gray-700">
                                <p class="text-white font-semibold">{{ $classroom->name }}</p>
                                <p class="text-sm text-gray-400">Giáo viên: {{ $classroom->teacher->name }}</p>
                                <form method="POST" action="{{ route('student.classrooms.join') }}" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $classroom->code }}">
                                    <button class="text-purple-400 hover:text-purple-300 text-sm" type="submit">
                                        Tham gia →
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">Không có lớp học nào.</p>
                @endif
            </div>
        </div>

        <!-- Joined Classrooms -->
        @if($approvedClassrooms->count() > 0)
            <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Lớp học của bạn ({{ $approvedClassrooms->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($approvedClassrooms as $classroom)
                        <div class="bg-gray-900 rounded p-4 border border-green-600">
                            <p class="text-white font-semibold">{{ $classroom->name }}</p>
                            <p class="text-sm text-gray-400 mb-3">Giáo viên: {{ $classroom->teacher->name }}</p>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('student.classrooms.leave', $classroom) }}">
                                    @csrf
                                    <button class="text-red-400 hover:text-red-300 text-sm" type="submit">
                                        Rời khỏi
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pending Classrooms -->
        @if($pendingClassrooms->count() > 0)
            <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Chờ duyệt ({{ $pendingClassrooms->count() }})</h2>
                <div class="space-y-3">
                    @foreach($pendingClassrooms as $classroom)
                        <div class="bg-gray-900 rounded p-4 border border-yellow-700">
                            <p class="text-white font-semibold">{{ $classroom->name }}</p>
                            <p class="text-sm text-gray-400 mb-3">Giáo viên: {{ $classroom->teacher->name }}</p>
                            <p class="text-yellow-400 text-sm mb-3">⏳ Đang chờ giáo viên duyệt yêu cầu...</p>
                            <form method="POST" action="{{ route('student.classrooms.leave', $classroom) }}">
                                @csrf
                                <button class="text-red-400 hover:text-red-300 text-sm" type="submit">
                                    Hủy yêu cầu
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Available Courses to Join -->
        @if($approvedClassrooms->count() > 0)
            <div class="bg-gray-800 border border-blue-600 rounded-lg p-6">
                <h2 class="text-xl font-bold text-white mb-4">Khóa học có sẵn</h2>
                @if($availableCourses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableCourses as $course)
                            <div class="bg-gray-900 rounded p-4 border border-blue-700">
                                <p class="text-white font-semibold">{{ $course->name }}</p>
                                <p class="text-sm text-gray-400 mb-2">{{ $course->description ?? 'Không có mô tả' }}</p>
                                <p class="text-xs text-gray-500 mb-3">Lớp: {{ $course->classroom->name }}</p>
                                
                                @if(in_array($course->id, $joinedCourseIds))
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('student.courses.leave', $course) }}" style="display:inline;">
                                            @csrf
                                            <button class="text-red-400 hover:text-red-300 text-sm" type="submit">
                                                Rời khỏi khóa học
                                            </button>
                                        </form>
                                        <a href="{{ route('student.courses.show', $course) }}" class="text-purple-400 hover:text-purple-300 text-sm">
                                            Xem chi tiết →
                                        </a>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('student.courses.join', $course) }}">
                                        @csrf
                                        <button class="text-green-400 hover:text-green-300 text-sm" type="submit">
                                            Tham gia khóa học
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">Chưa có khóa học nào. Vui lòng tham gia lớp trước.</p>
                @endif
            </div>
        @endif
    </div>
@endsection
