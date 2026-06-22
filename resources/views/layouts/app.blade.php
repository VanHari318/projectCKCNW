<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Online Learning Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-dark: #1a1a2e;
            --primary-darker: #0f0f1e;
            --accent-purple: #8b5cf6;
            --accent-purple-light: #a78bfa;
        }
        
        body {
            background-color: var(--primary-dark);
            color: #f0f0f0;
        }
        
        .navbar {
            background-color: var(--primary-darker);
            border-bottom: 2px solid var(--accent-purple);
        }
        
        .btn-primary {
            background-color: var(--accent-purple);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-purple-light);
            transform: translateY(-2px);
        }
        
        .form-input {
            background-color: #2a2a3e;
            border: 1px solid var(--accent-purple);
            color: #f0f0f0;
        }
        
        .form-input:focus {
            background-color: #333347;
            border-color: var(--accent-purple-light);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navbar -->
    <nav class="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="no-underline" title="{{ __('Quay về Dashboard') }}" aria-label="{{ __('Quay về Dashboard') }}">
                        <h1 class="text-2xl font-bold text-white cursor-pointer">
                            <span class="text-purple-500">Online</span> {{ __('Learning') }}
                        </h1>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Language selector -->
                    <div class="relative inline-block text-left">
                        <button id="lang-btn" type="button" class="inline-flex items-center px-3 py-1 rounded bg-gray-700 text-sm text-white hover:bg-gray-600" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2">{{ strtoupper(app()->getLocale()) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="lang-menu" class="origin-top-right absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">English</a>
                                <a href="{{ route('locale.switch', 'vi') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tiếng Việt</a>
                            </div>
                        </div>
                    </div>
                    @auth
                        <span class="text-gray-300">{{ __('Xin chào,') }} <span class="text-purple-400">{{ auth()->user()->name }}</span></span>
                        <span class="text-gray-500">|</span>

                        <!-- Profile Icon -->
                        <a href="{{ route('profile.show') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-purple-700 text-white hover:bg-purple-500 transition" title="{{ __('Hồ sơ cá nhân') }}" aria-label="{{ __('Hồ sơ cá nhân') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5Z" />
                            </svg>
                        </a>

                        @if(auth()->user()->isStudent())
                            <!-- Gradebook Icon -->
                            <a href="{{ route('student.info') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 text-white hover:bg-blue-500 transition" title="{{ __('Bảng điểm học sinh') }}" aria-label="{{ __('Bảng điểm học sinh') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                </svg>
                            </a>
                        @endif

                        <!-- Message Icon -->
                        <a href="{{ route('messages.index') }}" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-700 text-white hover:bg-purple-600 transition" title="{{ __('Tin nhắn') }}" aria-label="{{ __('Tin nhắn') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                            <span id="unread-messages-badge" class="absolute -top-1 -right-1 hidden items-center justify-center min-w-5 h-5 px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">0</span>
                        </a>

                        <!-- Group Message Icon -->
                        <a href="{{ route('group_messages.index') }}" class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-700 text-white hover:bg-purple-600 transition" title="{{ __('Nhóm chat khóa học') }}" aria-label="{{ __('Nhóm chat khóa học') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                {{ __('Logout') }}
                            </button>
                        </form>
                    @endauth
                    
                    @guest
                        <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300">{{ __('Login') }}</a>
                        <span class="text-gray-500">|</span>
                        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300">{{ __('Register') }}</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if($errors->any())
            <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4">
                <strong>Lỗi!</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('success'))
            <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-purple-600 text-center text-gray-400 py-6 mt-12">
        <p>&copy; 2026 Online Learning Platform. All rights reserved.</p>
    </footer>

    @auth
        <script>
            function updateUnreadMessageCount() {
                fetch("{{ route('messages.unread_count') }}")
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('unread-messages-badge');
                        if (badge) {
                            if (data.unread_count > 0) {
                                badge.textContent = data.unread_count;
                                badge.classList.remove('hidden');
                                badge.classList.add('inline-flex');
                            } else {
                                badge.classList.add('hidden');
                                badge.classList.remove('inline-flex');
                            }
                        }
                    })
                    .catch(error => console.error('Lỗi khi đếm tin nhắn:', error));
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateUnreadMessageCount();
                // Polling mỗi 5 giây để cập nhật số tin nhắn chưa đọc
                setInterval(updateUnreadMessageCount, 5000);
            });
        </script>
    @endauth
    <script>
        // Language menu toggle (works for guests and authenticated users)
        document.addEventListener('click', function(e) {
            const btn = document.getElementById('lang-btn');
            const menu = document.getElementById('lang-menu');
            if (!btn || !menu) {
                console.warn('Language button or menu not found in DOM');
                return;
            }
            
            // Check if click was on button or its children
            if (btn === e.target || btn.contains(e.target)) {
                console.log('Language button clicked, toggling menu...');
                menu.classList.toggle('hidden');
                e.stopPropagation();
            } else if (!menu.contains(e.target)) {
                // Click outside menu and button - hide menu
                if (!menu.classList.contains('hidden')) {
                    console.log('Click outside menu, hiding...');
                    menu.classList.add('hidden');
                }
            }
        });

        // Ensure script is loaded
        console.log('Language selector script loaded');
    </script>
</body>
</html>
