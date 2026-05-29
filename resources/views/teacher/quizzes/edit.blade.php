@extends('layouts.app')

@section('title', 'Sửa bài kiểm tra')

@section('content')
<div class="flex flex-col gap-6">
    <div>
        <a href="{{ route('teacher.manage') }}" class="text-purple-400 hover:text-purple-300 text-sm mb-4 inline-block">← Quay lại quản lý lớp</a>
        <h1 class="text-3xl font-bold text-white mb-2">Sửa bài kiểm tra</h1>
        <p class="text-gray-400">Khóa học: {{ $course->name }}</p>
    </div>

    <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <input type="text" name="title" class="w-full rounded form-input px-4 py-2" value="{{ $quiz->title }}" required>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <input type="number" name="duration" class="w-full rounded form-input px-4 py-2" value="{{ $quiz->duration }}" min="1" required>
            <input type="datetime-local" name="due_date" class="w-full rounded form-input px-4 py-2" value="{{ $quiz->due_date?->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="space-y-3" id="questionList">
            @foreach ($quiz->questions as $index => $question)
                @php
                    $type = $question['type'] ?? 'single';
                    $correct = $question['correct_options'] ?? [];
                @endphp
                <div class="bg-gray-900 p-4 rounded border border-gray-700" data-question>
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-white font-semibold">Câu {{ $index + 1 }}</h4>
                        <div class="flex gap-2">
                            <button type="button" class="text-xs text-purple-300" data-move="up">↑</button>
                            <button type="button" class="text-xs text-purple-300" data-move="down">↓</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="questions[{{ $index }}][question_text]" class="w-full rounded form-input px-3 py-2" value="{{ $question['question_text'] }}" required>
                        <select name="questions[{{ $index }}][type]" class="w-full rounded form-input px-3 py-2" data-question-type>
                            <option value="single" {{ $type === 'single' ? 'selected' : '' }}>Một đáp án</option>
                            <option value="multi" {{ $type === 'multi' ? 'selected' : '' }}>Nhiều đáp án</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        @foreach ($question['options'] as $optIndex => $optText)
                            <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}]" class="w-full rounded form-input px-3 py-2" value="{{ $optText }}" required>
                        @endforeach
                    </div>
                    <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2" data-correct-container>
                        @for ($optIndex = 0; $optIndex < 4; $optIndex++)
                            @php
                                $isChecked = in_array($optIndex, $correct, true);
                                $isRequired = $type === 'single' && $optIndex === 0;
                            @endphp
                            <label class="text-sm text-gray-300 flex items-center gap-2">
                                <input type="{{ $type === 'multi' ? 'checkbox' : 'radio' }}" name="questions[{{ $index }}][correct_options][]" value="{{ $optIndex }}" {{ $isChecked ? 'checked' : '' }} {{ $isRequired ? 'required' : '' }}>
                                {{ chr(65 + $optIndex) }} đúng
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="button" class="btn-primary px-4 py-2 rounded" id="addQuestion">+ Thêm câu hỏi</button>
            <button type="submit" class="btn-primary px-4 py-2 rounded">Lưu thay đổi</button>
        </div>
    </form>
</div>

<script>
(function () {
    const list = document.getElementById('questionList');
    const addButton = document.getElementById('addQuestion');

    function buildQuestion(index) {
        const wrapper = document.createElement('div');
        wrapper.className = 'bg-gray-900 p-4 rounded border border-gray-700';
        wrapper.setAttribute('data-question', '');
        wrapper.innerHTML =
            '<div class="flex justify-between items-center mb-3">' +
            '  <h4 class="text-white font-semibold">Câu ' + (index + 1) + '</h4>' +
            '  <div class="flex gap-2">' +
            '    <button type="button" class="text-xs text-purple-300" data-move="up">↑</button>' +
            '    <button type="button" class="text-xs text-purple-300" data-move="down">↓</button>' +
            '  </div>' +
            '</div>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">' +
            '  <input type="text" name="questions[' + index + '][question_text]" class="w-full rounded form-input px-3 py-2" placeholder="Nội dung câu hỏi" required>' +
            '  <select name="questions[' + index + '][type]" class="w-full rounded form-input px-3 py-2" data-question-type>' +
            '    <option value="single">Một đáp án</option>' +
            '    <option value="multi">Nhiều đáp án</option>' +
            '  </select>' +
            '</div>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">' +
            '  <input type="text" name="questions[' + index + '][options][0]" class="w-full rounded form-input px-3 py-2" placeholder="Đáp án A" required>' +
            '  <input type="text" name="questions[' + index + '][options][1]" class="w-full rounded form-input px-3 py-2" placeholder="Đáp án B" required>' +
            '  <input type="text" name="questions[' + index + '][options][2]" class="w-full rounded form-input px-3 py-2" placeholder="Đáp án C" required>' +
            '  <input type="text" name="questions[' + index + '][options][3]" class="w-full rounded form-input px-3 py-2" placeholder="Đáp án D" required>' +
            '</div>' +
            '<div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2" data-correct-container>' +
            '  <label class="text-sm text-gray-300 flex items-center gap-2"><input type="radio" name="questions[' + index + '][correct_options][]" value="0" required> A đúng</label>' +
            '  <label class="text-sm text-gray-300 flex items-center gap-2"><input type="radio" name="questions[' + index + '][correct_options][]" value="1"> B đúng</label>' +
            '  <label class="text-sm text-gray-300 flex items-center gap-2"><input type="radio" name="questions[' + index + '][correct_options][]" value="2"> C đúng</label>' +
            '  <label class="text-sm text-gray-300 flex items-center gap-2"><input type="radio" name="questions[' + index + '][correct_options][]" value="3"> D đúng</label>' +
            '</div>';
        return wrapper;
    }

    function renumber() {
        const items = list.querySelectorAll('[data-question]');
        items.forEach((item, index) => {
            item.querySelector('h4').textContent = 'Câu ' + (index + 1);
            item.querySelectorAll('input[name], select[name]').forEach((input) => {
                const name = input.getAttribute('name');
                if (!name) return;
                input.setAttribute('name', name.replace(/questions\[\d+\]/, 'questions[' + index + ']'));
            });
        });
    }

    function updateCorrectInputs(container, type) {
        const inputs = container.querySelectorAll('input');
        inputs.forEach((input) => {
            input.type = type === 'multi' ? 'checkbox' : 'radio';
            input.required = type === 'single';
        });
    }

    addButton.addEventListener('click', () => {
        list.appendChild(buildQuestion(list.querySelectorAll('[data-question]').length));
    });

    list.addEventListener('change', (event) => {
        const target = event.target;
        if (target && target.matches('[data-question-type]')) {
            const container = target.closest('[data-question]')?.querySelector('[data-correct-container]');
            if (container) {
                updateCorrectInputs(container, target.value);
            }
        }
    });

    list.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-move]');
        if (!button) return;
        const question = button.closest('[data-question]');
        if (!question) return;
        if (button.dataset.move === 'up' && question.previousElementSibling) {
            list.insertBefore(question, question.previousElementSibling);
        }
        if (button.dataset.move === 'down' && question.nextElementSibling) {
            list.insertBefore(question.nextElementSibling, question);
        }
        renumber();
    });
})();
</script>
@endsection
