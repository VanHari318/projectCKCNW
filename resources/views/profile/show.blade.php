@extends('layouts.app')

@section('title', 'Hồ sơ của tôi')

@section('content')
<div class="space-y-6">
    <div class="bg-blue-900 rounded-lg p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4 {{ $user->isTeacher() ? 'bg-purple-600' : ($user->isStudent() ? 'bg-green-600' : 'bg-gray-700') }}">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-200 mt-1">{{ $user->email }}</p>
                    <span class="inline-block mt-3 px-3 py-1 rounded-full text-sm font-medium text-white {{ $user->isTeacher() ? 'bg-gradient-to-r from-purple-600 to-purple-500' : ($user->isStudent() ? 'bg-gradient-to-r from-green-600 to-green-500' : 'bg-gray-700') }}">@if($user->isStudent()) Học sinh @elseif($user->isTeacher()) Giáo viên @else Người dùng @endif</span>
                </div>
            </div>
            <div class="text-right space-x-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded bg-gray-700 hover:bg-gray-600 text-white">Thoát</a>
                <button id="edit-profile-btn" type="button" class="btn-primary px-4 py-2 rounded">Chỉnh sửa</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-yellow-700 p-4 rounded-lg shadow-md border-l-4 {{ $user->isTeacher() ? 'border-green-500' : ($user->isStudent() ? 'border-blue-500' : 'border-gray-600') }}">
            <h3 class="text-lg font-semibold text-white">Thông tin cơ bản</h3>
            <div class="mt-3 text-gray-200">
                <p><strong>Tên:</strong> <span class="ml-2 text-white">{{ $user->name }}</span></p>
                <p class="mt-2"><strong>Email (TK):</strong> <span class="ml-2 text-white">{{ $user->email }}</span></p>
                <p class="mt-2"><strong>Vai trò:</strong> <span class="ml-2 text-white">@if($user->isStudent()) Học sinh @elseif($user->isTeacher()) Giáo viên @else Khác @endif</span></p>
            </div>
        </div>

        @if($user->isStudent())
        <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-white">Lớp đã tham gia</h3>
            <div class="mt-3">
                @if(isset($classrooms) && $classrooms->count())
                <ul class="space-y-2">
                    @foreach($classrooms as $c)
                    <li class="p-3 bg-gray-800 rounded flex items-center justify-between hover:bg-gray-700">
                        <div>
                            <div class="font-medium text-white">{{ $c->name }}</div>
                            <div class="text-gray-400 text-sm">Mã: {{ $c->code }}</div>
                        </div>
                        <a href="{{ route('student.classrooms.show', $c->id) }}" class="text-blue-300 hover:underline">Xem</a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-gray-400">Chưa tham gia lớp nào</p>
                @endif
            </div>
        </div>

        <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-white">Khóa học đã tham gia</h3>
            <div class="mt-3">
                @if(isset($courses) && $courses->count())
                <ul class="space-y-2">
                    @foreach($courses as $course)
                    <li class="p-3 bg-gray-800 rounded flex items-center justify-between hover:bg-gray-700">
                        <div>
                            <div class="font-medium text-white">{{ $course->name }}</div>
                        </div>
                        <a href="{{ route('student.courses.show', $course->id) }}" class="text-blue-300 hover:underline">Xem</a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-gray-400">Chưa tham gia khóa học nào</p>
                @endif
            </div>
        </div>
        @elseif($user->isTeacher())
        <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-white">Lớp đang quản lý</h3>
            <div class="mt-3">
                @if(isset($classroom) && $classroom)
                <div class="p-3 bg-gray-800 rounded">
                    <div class="font-medium text-white">{{ $classroom->name }}</div>
                    <div class="text-gray-400 text-sm">Mã: {{ $classroom->code }}</div>
                </div>
                @else
                <p class="text-gray-400">Chưa có lớp quản lý</p>
                @endif
            </div>
        </div>

        <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-white">Khóa học</h3>
            <div class="mt-3">
                @if(isset($courses) && $courses->count())
                <ul class="space-y-2">
                    @foreach($courses as $course)
                    <li class="p-3 bg-gray-800 rounded flex items-center justify-between hover:bg-gray-700">
                        <div class="font-medium text-white">{{ $course->name }}</div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-gray-400">Chưa có khóa học nào</p>
                @endif
            </div>
        </div>
        @endif

        <div id="profile-edit-form" class="hidden bg-gray-900 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-white mb-4">Chỉnh sửa thông tin</h3>
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-300">Tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input mt-1 block w-full rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input mt-1 block w-full rounded p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-input mt-1 block w-full rounded p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-input mt-1 block w-full rounded p-2">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="btn-primary px-4 py-2 rounded">Lưu thay đổi</button>
                    <button type="button" id="cancel-edit-btn" class="ml-2 px-4 py-2 rounded bg-gray-700 hover:bg-gray-600">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-edit-btn');
        const formDiv = document.getElementById('profile-edit-form');

        if (editBtn && formDiv) {
            editBtn.addEventListener('click', function() {
                const wasHidden = formDiv.classList.contains('hidden');
                formDiv.classList.remove('hidden');
                // Wait a tick for layout, then scroll and focus
                setTimeout(() => {
                    const firstInput = document.querySelector('#profile-edit-form input[name="name"]');
                    if (firstInput) {
                        firstInput.focus();
                    }
                    formDiv.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
                // If it was visible, hide it instead
                if (!wasHidden) {
                    formDiv.classList.add('hidden');
                }
            });
        }

        if (cancelBtn && formDiv) {
            cancelBtn.addEventListener('click', function() {
                formDiv.classList.add('hidden');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>

@endsection