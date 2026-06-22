@extends('layouts.app')

@section('title', __('Chỉnh sửa hồ sơ'))

@section('content')
<div class="max-w-2xl mx-auto bg-gray-900 p-6 rounded-lg shadow-md border border-purple-900">
    <h2 class="text-2xl font-bold mb-6 text-white border-b border-purple-600 pb-3">{{ __('Chỉnh sửa thông tin cá nhân') }}</h2>
    
    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Tên') }}</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input mt-1 block w-full rounded p-2.5 bg-gray-800 border border-purple-500 text-white focus:outline-none focus:ring-2 focus:ring-purple-600" readonly>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Email') }}</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input mt-1 block w-full rounded p-2.5 bg-gray-800 border border-purple-500 text-white focus:outline-none focus:ring-2 focus:ring-purple-600" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Mật khẩu mới (để trống nếu không đổi)') }}</label>
            <input type="password" name="password" class="form-input mt-1 block w-full rounded p-2.5 bg-gray-800 border border-purple-500 text-white focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Xác nhận mật khẩu') }}</label>
            <input type="password" name="password_confirmation" class="form-input mt-1 block w-full rounded p-2.5 bg-gray-800 border border-purple-500 text-white focus:outline-none focus:ring-2 focus:ring-purple-600">
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-800">
            <a href="{{ route('profile.show') }}" class="px-4 py-2.5 rounded bg-gray-700 hover:bg-gray-600 text-white transition duration-200">{{ __('Hủy') }}</a>
            <button type="submit" class="btn-primary px-5 py-2.5 rounded font-semibold transition duration-200">{{ __('Lưu thay đổi') }}</button>
        </div>
    </form>
</div>
@endsection