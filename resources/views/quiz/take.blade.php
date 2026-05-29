@extends('layouts.app')

@section('title', 'Làm bài kiểm tra')

@section('content')
<div class="flex flex-col gap-6">
    <div>
        <a href="{{ route('student.courses.show', $course) }}" class="text-purple-400 hover:text-purple-300 text-sm mb-4 inline-block">← Quay lại khóa học</a>
        <h1 class="text-3xl font-bold text-white mb-2">{{ $quiz->title }}</h1>
        <p class="text-gray-400">Thời lượng: {{ $quiz->duration }} phút</p>
        <p class="text-gray-400">Hạn nộp: {{ $quiz->due_date?->format('d/m/Y H:i') }}</p>
    </div>

    <form method="POST" action="{{ route('student.quizzes.submit', $quiz) }}" class="space-y-4">
        @csrf

        @foreach ($quiz->questions as $index => $question)
            @php
                $type = $question['type'] ?? 'single';
            @endphp
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-white mb-2">Câu {{ $index + 1 }}</h3>
                <p class="text-gray-300 mb-3">{{ $question['question_text'] }}</p>

                <div class="space-y-2">
                    @foreach ($question['options'] as $optionIndex => $option)
                        <label class="flex items-center gap-2 text-gray-200">
                            @if ($type === 'multi')
                                <input type="checkbox" name="answers[{{ $index }}][]" value="{{ $optionIndex }}" class="rounded border-gray-600">
                            @else
                                <input type="radio" name="answers[{{ $index }}]" value="{{ $optionIndex }}" class="border-gray-600">
                            @endif
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn-primary px-6 py-2 rounded">Nộp bài</button>
    </form>
</div>
@endsection
