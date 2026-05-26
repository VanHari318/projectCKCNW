<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Học Sinh</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <nav class="bg-gray-800 border-b-2 border-purple-600">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">
                <span class="text-purple-500">Online</span> Learning
            </h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-300">{{ auth()->user()->name }}</span>
                <span class="px-3 py-1 rounded-full bg-purple-600 text-white text-sm">Học Sinh</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-white mb-2">Bảng Điều Khiển <span class="text-purple-500">Học Sinh</span></h1>
        <p class="text-gray-400 mb-8">Xem lớp học, khóa học và điểm số của bạn</p>

        <!-- Welcome Box -->
        <div class="bg-gradient-to-r from-purple-900 to-purple-800 rounded-lg p-8 mb-8 border border-purple-600">
            <h2 class="text-2xl font-bold text-white mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-purple-100">Bạn đang đăng nhập với tư cách <strong>Học Sinh</strong></p>
        </div>

        <!-- Student Role Display -->
        <div class="text-center p-4 bg-purple-900 bg-opacity-50 rounded-lg border border-purple-600 mb-8">
            <p class="text-purple-200 text-lg">
                <strong>Echo của vai trò:</strong> <span class="font-mono bg-gray-800 px-3 py-1 rounded">student</span>
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-green-900 rounded-lg p-6 border border-green-600">
                <p class="text-gray-300 text-sm">Lớp (Duyệt)</p>
                <p class="text-3xl font-bold text-green-200">{{ $joinedClassrooms->count() }}</p>
            </div>
            <div class="bg-yellow-900 rounded-lg p-6 border border-yellow-600">
                <p class="text-gray-300 text-sm">Chờ Duyệt</p>
                <p class="text-3xl font-bold text-yellow-200">{{ $pendingClassrooms->count() }}</p>
            </div>
            <div class="bg-blue-900 rounded-lg p-6 border border-blue-600">
                <p class="text-gray-300 text-sm">Khóa Học</p>
                <p class="text-3xl font-bold text-blue-200">{{ $joinedCourses->count() }}</p>
            </div>
            <div class="bg-pink-900 rounded-lg p-6 border border-pink-600">
                <p class="text-gray-300 text-sm">Điểm TB</p>
                <p class="text-3xl font-bold text-pink-200">{{ $averageQuizScore ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Classrooms -->
        @if ($joinedClassrooms->count() > 0)
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-white mb-4">📚 Lớp Học Của Bạn</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($joinedClassrooms as $classroom)
                        <div class="bg-gray-800 rounded-lg p-6 border border-purple-600">
                            <h4 class="text-xl font-bold text-white mb-2">{{ $classroom->name }}</h4>
                            <p class="text-gray-400 text-sm mb-3">{{ $classroom->description ?? 'Không có mô tả' }}</p>
                            <p class="text-sm text-gray-300">👨‍🏫 {{ $classroom->teacher->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pending Classrooms -->
        @if ($pendingClassrooms->count() > 0)
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-white mb-4">⏳ Yêu Cầu Tham Gia Chờ Duyệt</h3>
                <div class="space-y-3">
                    @foreach ($pendingClassrooms as $classroom)
                        <div class="bg-gray-800 rounded-lg p-4 border border-yellow-600">
                            <h4 class="text-lg font-bold text-white">{{ $classroom->name }}</h4>
                            <p class="text-gray-400 text-sm">👨‍🏫 {{ $classroom->teacher->name }}</p>
                            <p class="text-yellow-400 text-sm">Chờ duyệt...</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Courses -->
        @if ($joinedCourses->count() > 0)
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-white mb-4">📖 Khóa Học</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($joinedCourses as $course)
                        <div class="bg-gray-800 rounded-lg p-6 border border-blue-600">
                            <h4 class="text-xl font-bold text-white mb-2">{{ $course->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ $course->description ?? 'Không có mô tả' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
