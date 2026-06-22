@extends('layouts.app')

@section('title', __('Hồ sơ của tôi'))

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
                    <span class="inline-block mt-3 px-3 py-1 rounded-full text-sm font-medium text-white {{ $user->isTeacher() ? 'bg-gradient-to-r from-purple-600 to-purple-500' : ($user->isStudent() ? 'bg-gradient-to-r from-green-600 to-green-500' : 'bg-gray-700') }}">@if($user->isStudent()) {{ __('Học sinh') }} @elseif($user->isTeacher()) {{ __('Giáo viên') }} @else {{ __('Người dùng') }} @endif</span>
                </div>
            </div>
            <div class="text-right space-x-2">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded bg-gray-700 hover:bg-gray-600 text-white">{{__('Thoát')}}</a>
                <a href="{{ route('profile.edit') }}" class="btn-primary px-4 py-2 rounded inline-block">{{__('Sửa thông tin')}}</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-yellow-700 p-4 rounded-lg shadow-md border-l-4 {{ $user->isTeacher() ? 'border-green-500' : ($user->isStudent() ? 'border-blue-500' : 'border-gray-600') }}">
            <h3 class="text-lg font-semibold text-white">{{ __('Thông tin cơ bản') }}</h3>
            <div class="mt-3 text-gray-200">
                <p><strong>{{ __('Tên:') }}</strong> <span class="ml-2 text-white">{{ $user->name }}</span></p>
                <p class="mt-2"><strong>{{ __('Email (TK):') }}</strong> <span class="ml-2 text-white">{{ $user->email }}</span></p>
                <p class="mt-2"><strong>{{ __('Vai trò:') }}</strong> <span class="ml-2 text-white">@if($user->isStudent()) {{ __('Học sinh') }} @elseif($user->isTeacher()) {{ __('Giáo viên') }} @else {{ __('Khác') }} @endif</span></p>
            </div>
        </div>

        @if($user->isStudent())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-gray-900">
                <h3 class="text-lg font-semibold text-white">{{ __('Lớp đã tham gia') }}</h3>
                <div class="mt-3">
                    @if(isset($classrooms) && $classrooms->count())
                    <ul class="space-y-2">
                        @foreach($classrooms as $c)
                        <li class="p-3 bg-gray-800 rounded flex items-center justify-between hover:bg-gray-700">
                            <div>
                                <div class="font-medium text-white">{{ $c->name }}</div>
                                <div class="text-gray-400 text-sm">{{ __('Mã:') }} {{ $c->code }}</div>
                            </div>
                            <a href="{{ route('student.classrooms.show', $c->id) }}" class="text-blue-300 hover:underline">{{ __('Xem') }}</a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-400">{{ __('Chưa tham gia lớp nào') }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-gray-900">
                <h3 class="text-lg font-semibold text-white">{{ __('Khóa học đã tham gia') }}</h3>
                <div class="mt-3">
                    @if(isset($courses) && $courses->count())
                    <ul class="space-y-2">
                        @foreach($courses as $course)
                        <li class="p-3 bg-gray-800 rounded flex items-center justify-between hover:bg-gray-700">
                            <div>
                                <div class="font-medium text-white">{{ $course->name }}</div>
                            </div>
                            <a href="{{ route('student.courses.show', $course->id) }}" class="text-blue-300 hover:underline">{{ __('Xem') }}</a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-400">{{ __('Chưa tham gia khóa học nào') }}</p>
                    @endif
                </div>
            </div>
        </div>
            @elseif($user->isTeacher())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-white">{{ __('Lớp đang quản lý') }}</h3>
                    <div class="mt-3">
                        @if(isset($classroom) && $classroom)
                        <div class="p-3 bg-gray-800 rounded">
                            <div class="font-medium text-white">{{ $classroom->name }}</div>
                            <div class="text-gray-400 text-sm">{{ __('Mã:') }} {{ $classroom->code }}</div>
                        </div>
                        @else
                        <p class="text-gray-400">{{ __('Chưa có lớp quản lý') }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-900 p-4 rounded-lg shadow-md border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-white">{{ __('Khóa học') }}</h3>
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
                        <p class="text-gray-400">{{ __('Chưa có khóa học nào') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @endsection