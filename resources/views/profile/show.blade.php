@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Header with profile info -->
    <div class="bg-gray-800/60 backdrop-blur-md border border-purple-500/40 rounded-2xl shadow-xl p-8 mb-8 relative overflow-hidden transition hover:border-purple-500/60">
        <!-- Background Glow -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-center gap-6 relative z-10">
            <!-- Profile Avatar Placeholder with Initial -->
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg border border-purple-400/30">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            
            <div class="text-center sm:text-left">
                <h2 class="text-2xl font-bold text-white mb-1">{{ $user->name }}</h2>
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-1.5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isTeacher() ? 'bg-purple-900/60 text-purple-200 border border-purple-500/30' : 'bg-blue-900/60 text-blue-200 border border-blue-500/30' }}">
                        {{ $user->isTeacher() ? 'Giáo viên' : 'Học sinh' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-700/60 text-gray-300 border border-gray-600/30">
                        Thành viên từ: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Chưa rõ' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-gray-800/40 backdrop-blur-md border border-gray-700/60 rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Chỉnh sửa thông tin
        </h3>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Email (Locked) -->
            <div>
                <label class="block text-sm font-semibold text-gray-400 mb-2 flex items-center gap-1.5">
                    Địa chỉ Email
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-gray-500" title="Trường này không thể thay đổi">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </label>
                <input type="email" value="{{ $user->email }}" disabled 
                    class="w-full rounded-xl bg-gray-900/60 border border-gray-800 text-gray-500 px-4 py-3 cursor-not-allowed select-none focus:outline-none" />
                <p class="text-xs text-gray-500 mt-1">Email đăng ký tài khoản không thể thay đổi.</p>
            </div>

            <!-- Role (Locked) -->
            <div>
                <label class="block text-sm font-semibold text-gray-400 mb-2 flex items-center gap-1.5">
                    Vai trò tài khoản
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-gray-500" title="Trường này không thể thay đổi">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                    </svg>
                </label>
                <input type="text" value="{{ $user->isTeacher() ? 'Giáo viên' : 'Học sinh' }}" disabled 
                    class="w-full rounded-xl bg-gray-900/60 border border-gray-800 text-gray-500 px-4 py-3 cursor-not-allowed select-none focus:outline-none" />
            </div>

            <!-- Full Name (Editable) -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">Họ và tên</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-xl form-input px-4 py-3 transition duration-200 border-gray-700/60 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" />
            </div>

            <hr class="border-gray-700/50 my-6">

            <!-- Password Fields Wrapper -->
            <div>
                <button type="button" id="togglePasswordBtn" class="text-sm text-purple-400 hover:text-purple-300 font-semibold flex items-center gap-1 focus:outline-none mb-4">
                    <span>Đổi mật khẩu tài khoản?</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform duration-200" id="arrowIcon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div id="passwordFields" class="hidden space-y-4 transition-all duration-300">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" placeholder="Nhập ít nhất 6 ký tự"
                            class="w-full rounded-xl form-input px-4 py-3 transition duration-200 border-gray-700/60 focus:border-purple-500" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Nhập lại mật khẩu mới"
                            class="w-full rounded-xl form-input px-4 py-3 transition duration-200 border-gray-700/60 focus:border-purple-500" />
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition font-medium">
                    ← Quay lại Dashboard
                </a>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl font-bold shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <!-- Student Grades Quick Link -->
    @if($user->isStudent())
        <div class="mt-6 bg-gradient-to-r from-blue-900/40 to-purple-900/40 backdrop-blur-md border border-blue-500/30 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
            <div>
                <h4 class="text-white font-bold text-lg mb-1">Bảng Điểm Cá Nhân</h4>
                <p class="text-sm text-gray-300">Xem và theo dõi kết quả làm bài tập, bài thi trắc nghiệm của bạn.</p>
            </div>
            <a href="{{ route('student.info') }}" class="whitespace-nowrap bg-blue-600 hover:bg-blue-500 text-white font-bold px-5 py-2.5 rounded-xl border border-blue-400/30 transition shadow-lg shadow-blue-500/20 hover:-translate-y-0.5">
                Xem Bảng Điểm 📊
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordFields = document.getElementById('passwordFields');
        const arrowIcon = document.getElementById('arrowIcon');
        
        toggleBtn.addEventListener('click', function() {
            const isHidden = passwordFields.classList.contains('hidden');
            if (isHidden) {
                passwordFields.classList.remove('hidden');
                arrowIcon.classList.add('rotate-180');
            } else {
                passwordFields.classList.add('hidden');
                arrowIcon.classList.remove('rotate-180');
                // Clear password inputs when collapsed
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }
        });
    });
</script>
@endsection
