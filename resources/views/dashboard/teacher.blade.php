<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giáo Viên</title>
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
                <span class="px-3 py-1 rounded-full bg-purple-600 text-white text-sm">Giáo Viên</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-white mb-2">Bảng Điều Khiển <span class="text-purple-500">Giáo Viên</span></h1>
        <p class="text-gray-400 mb-8">Quản lý lớp học, học sinh và bài giảng</p>

        <!-- Welcome Box -->
        <div class="bg-gradient-to-r from-purple-900 to-purple-800 rounded-lg p-8 mb-8 border border-purple-600">
            <h2 class="text-2xl font-bold text-white mb-2">Xin chào, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-purple-100">Bạn đang đăng nhập với tư cách <strong>Giáo Viên</strong></p>
        </div>

        <!-- Teacher Role Display -->
        <div class="text-center p-4 bg-purple-900 bg-opacity-50 rounded-lg border border-purple-600 mb-8">
            <p class="text-purple-200 text-lg">
                <strong>Echo của vai trò:</strong> <span class="font-mono bg-gray-800 px-3 py-1 rounded">teacher</span>
            </p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

        @if ($classroom)
            <div class="bg-gray-800 rounded-lg p-8 border border-purple-600">
                <h3 class="text-2xl font-bold text-white mb-4">📖 {{ $classroom->name }}</h3>
                <p class="text-gray-300 mb-4">{{ $classroom->description ?? 'Không có mô tả' }}</p>
                <button class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded">Quản Lý</button>
            </div>
        @else
            <div class="bg-gray-800 rounded-lg p-8 border-2 border-dashed border-purple-600 text-center">
                <p class="text-gray-400 mb-4">Bạn chưa tạo lớp học nào</p>
                <button class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-6 rounded">+ Tạo Lớp</button>
            </div>
        @endif
    </div>
</body>
</html>
