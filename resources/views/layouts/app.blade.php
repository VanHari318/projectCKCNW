<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <a href="{{ route('dashboard') }}" class="no-underline" title="Quay về Dashboard" aria-label="Quay về Dashboard">
                        <h1 class="text-2xl font-bold text-white cursor-pointer">
                            <span class="text-purple-500">Online</span> Learning
                        </h1>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-300">Xin chào, <span class="text-purple-400">{{ auth()->user()->name }}</span></span>
                        <span class="text-gray-500">|</span>
                        <span class="px-3 py-1 rounded-full bg-purple-600 text-white text-sm">
                            {{ auth()->user()->role === 'teacher' ? 'Giáo viên' : 'Học sinh' }}
                        </span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                Đăng xuất
                            </button>
                        </form>
                    @endauth
                    
                    @guest
                        <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300">Đăng nhập</a>
                        <span class="text-gray-500">|</span>
                        <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300">Đăng kí</a>
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
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-purple-600 text-center text-gray-400 py-6 mt-12">
        <p>&copy; 2026 Online Learning Platform. All rights reserved.</p>
    </footer>
</body>
</html>
