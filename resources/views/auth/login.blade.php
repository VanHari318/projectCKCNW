<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Đăng Nhập') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-4xl font-bold text-white mb-2">
                    <span class="text-purple-500">{{ __('Đăng Nhập') }}</span> {{ __('Tài Khoản') }}
                </h2>
                <p class="text-gray-400">{{ __('Đăng nhập để tiếp tục học tập') }}</p>
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

            @if(session('success'))
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-6 bg-gray-800 p-6 rounded-lg">
                @csrf

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
                        autofocus
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
                        placeholder="{{ __('Nhập mật khẩu') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-purple-500 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                </div>

                <!-- Remember Me -->
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 text-purple-500 bg-gray-700 border-gray-600 rounded"
                    >
                    <span class="ml-2 text-sm text-gray-300">{{ __('Ghi nhớ tôi') }}</span>
                </label>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg"
                >
                    {{ __('Đăng Nhập') }}
                </button>

                <!-- Register Link -->
                <p class="text-center text-gray-400 text-sm">
                    {{ __('Chưa có tài khoản?') }}
                    <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-medium">
                        {{ __('Đăng Kí') }}
                    </a>
                </p>
            </form>

            <!-- Demo Info -->
            <div class="mt-6 p-4 bg-purple-900 bg-opacity-30 border border-purple-700 rounded-lg text-sm text-gray-300">
                <p class="font-semibold text-purple-300 mb-2">{{ __('Tài khoản demo:') }}</p>
                <p>👨‍🏫 Teacher: teacher@example.com / password</p>
                <p>👨‍🎓 Student: student@example.com / password</p>
            </div>
        </div>
    </div>
</body>
</html>
