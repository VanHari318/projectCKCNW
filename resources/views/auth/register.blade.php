<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Đăng Kí Tài Khoản') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-4xl font-bold text-white mb-2">
                    <span class="text-purple-500">{{ __('Đăng Kí') }}</span> {{ __('Tài Khoản') }}
                </h2>
                <p class="text-gray-400">{{ __('Tham gia nền tảng học tập trực tuyến') }}</p>
            </div>

            <!-- Flash Messages -->
            @if($errors->any())
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded">
                    <strong>{{ __('Lỗi!') }}</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-6 bg-gray-800 p-6 rounded-lg">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('Họ và Tên') }}
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        placeholder="{{ __('Nhập chính xác họ và tên') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-purple-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required
                        placeholder="email@example.com"
                        class="w-full px-4 py-2 bg-gray-700 border border-purple-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('Mật Khẩu') }}
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="{{ __('Ít nhất 6 ký tự') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-purple-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('Xác Nhận Mật Khẩu') }}
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                        placeholder="{{ __('Nhập lại mật khẩu') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-purple-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">
                        {{ __('Bạn là ai?') }}
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-purple-500">
                            <input 
                                type="radio" 
                                name="role" 
                                value="teacher"
                                {{ old('role') === 'teacher' ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-500"
                            >
                            <span class="ml-2 text-white">{{ __('Giáo Viên') }}</span>
                        </label>
                        <label class="flex items-center p-3 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-purple-500">
                            <input 
                                type="radio" 
                                name="role" 
                                value="student"
                                {{ old('role') === 'student' ? 'checked' : '' }}
                                class="w-4 h-4 text-purple-500"
                            >
                            <span class="ml-2 text-white">{{ __('Học Sinh') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg"
                >
                    {{ __('Đăng Kí') }}
                </button>

                <!-- Login Link -->
                <p class="text-center text-gray-400 text-sm">
                    {{ __('Đã có tài khoản?') }}
                    <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-medium">
                        {{ __('Đăng Nhập') }}
                    </a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
