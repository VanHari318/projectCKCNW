@extends('layouts.app')

@section('title', 'Tạo Bài Kiểm Tra')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-white mb-8">Tạo Bài Kiểm Tra Mới</h1>

    <form method="POST" action="{{ route('teacher.quizzes.store') }}" class="space-y-6 bg-gray-800 border border-purple-600 rounded-lg p-8">
        @csrf

        <!-- Course Selection -->
        <div>
            <label class="block text-white font-semibold mb-2">Chọn Khóa Học *</label>
            <select name="course_id" required class="form-input w-full px-4 py-2 rounded">
                <option value="">-- Chọn khóa học --</option>
                @forelse($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @empty
                    <option disabled>Không có khóa học nào</option>
                @endforelse
            </select>
            @error('course_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Title -->
        <div>
            <label class="block text-white font-semibold mb-2">Tiêu Đề *</label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ví dụ: Kiểm tra chương 1" class="form-input w-full px-4 py-2 rounded">
            @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Duration -->
        <div>
            <label class="block text-white font-semibold mb-2">Thời Lượng (phút) *</label>
            <input type="number" name="duration" value="{{ old('duration', 30) }}" required min="1" class="form-input w-full px-4 py-2 rounded">
            @error('duration') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Due Date -->
        <div>
            <label class="block text-white font-semibold mb-2">Hạn Nộp *</label>
            <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" required class="form-input w-full px-4 py-2 rounded">
            @error('due_date') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Questions -->
        <div id="questions-container" class="space-y-6">
            <label class="block text-white font-semibold mb-4">Câu Hỏi *</label>
            <div class="bg-gray-900 rounded p-4 border border-gray-700" id="question-0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-purple-300 font-semibold">Câu hỏi 1</h3>
                    <button type="button" onclick="removeQuestion(0)" class="text-red-400 hover:text-red-300 text-sm" style="display: none;">Xóa</button>
                </div>

                <div class="space-y-3">
                    <input type="text" name="questions[0][question_text]" placeholder="Nhập câu hỏi" required class="form-input w-full px-3 py-2 rounded">
                    
                    <select name="questions[0][type]" onchange="updateQuestionType(0)" class="form-input w-full px-3 py-2 rounded">
                        <option value="single">Lựa chọn đơn</option>
                        <option value="multi">Lựa chọn nhiều</option>
                    </select>

                    <div class="space-y-2">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex gap-2">
                                <input type="text" name="questions[0][options][{{ $i }}]" placeholder="Phương án {{ $i + 1 }}" required class="form-input flex-1 px-3 py-2 rounded">
                                <label class="flex items-center text-white">
                                    <input type="checkbox" name="questions[0][correct_options][]" value="{{ $i }}" class="mr-2">
                                    Đúng
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <button type="button" onclick="addQuestion()" class="text-purple-400 hover:text-purple-300">+ Thêm Câu Hỏi</button>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4">
            <button type="submit" name="status" value="draft" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded">
                Lưu Nháp
            </button>
            <button type="submit" name="status" value="published" class="btn-primary px-6 py-2 rounded">
                Tạo & Xuất Bản
            </button>
            <a href="{{ route('teacher.quizzes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
let questionCount = 1;

function addQuestion() {
    const container = document.getElementById('questions-container');
    const newQuestion = document.createElement('div');
    newQuestion.id = `question-${questionCount}`;
    newQuestion.className = 'bg-gray-900 rounded p-4 border border-gray-700';
    newQuestion.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-purple-300 font-semibold">Câu hỏi ${questionCount + 1}</h3>
            <button type="button" onclick="removeQuestion(${questionCount})" class="text-red-400 hover:text-red-300 text-sm">Xóa</button>
        </div>
        <div class="space-y-3">
            <input type="text" name="questions[${questionCount}][question_text]" placeholder="Nhập câu hỏi" required class="form-input w-full px-3 py-2 rounded">
            <select name="questions[${questionCount}][type]" onchange="updateQuestionType(${questionCount})" class="form-input w-full px-3 py-2 rounded">
                <option value="single">Lựa chọn đơn</option>
                <option value="multi">Lựa chọn nhiều</option>
            </select>
            <div class="space-y-2">
                <div class="flex gap-2">
                    <input type="text" name="questions[${questionCount}][options][0]" placeholder="Phương án 1" required class="form-input flex-1 px-3 py-2 rounded">
                    <label class="flex items-center text-white">
                        <input type="checkbox" name="questions[${questionCount}][correct_options][]" value="0" class="mr-2">
                        Đúng
                    </label>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="questions[${questionCount}][options][1]" placeholder="Phương án 2" required class="form-input flex-1 px-3 py-2 rounded">
                    <label class="flex items-center text-white">
                        <input type="checkbox" name="questions[${questionCount}][correct_options][]" value="1" class="mr-2">
                        Đúng
                    </label>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="questions[${questionCount}][options][2]" placeholder="Phương án 3" required class="form-input flex-1 px-3 py-2 rounded">
                    <label class="flex items-center text-white">
                        <input type="checkbox" name="questions[${questionCount}][correct_options][]" value="2" class="mr-2">
                        Đúng
                    </label>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="questions[${questionCount}][options][3]" placeholder="Phương án 4" required class="form-input flex-1 px-3 py-2 rounded">
                    <label class="flex items-center text-white">
                        <input type="checkbox" name="questions[${questionCount}][correct_options][]" value="3" class="mr-2">
                        Đúng
                    </label>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newQuestion);
    questionCount++;
}

function removeQuestion(index) {
    const element = document.getElementById(`question-${index}`);
    if (element) {
        element.remove();
    }
}

function updateQuestionType(index) {
    // Logic để xử lý loại câu hỏi nếu cần
}
</script>
@endsection
