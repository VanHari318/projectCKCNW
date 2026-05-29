@extends('layouts.app')

@section('title', 'Xem lại bài kiểm tra')

@section('content')
<div class="flex flex-col gap-6">
    <div>
        <a href="{{ route('student.courses.show', $course) }}" class="text-purple-400 hover:text-purple-300 text-sm mb-4 inline-block">← Quay lại khóa học</a>
        <h1 class="text-3xl font-bold text-white mb-2">{{ $quiz->title }}</h1>
        <p class="text-gray-400">Điểm: <span class="text-green-400 font-bold">{{ $grade->score }}/10</span></p>
    </div>

    @foreach ($details as $index => $item)
        @php
            $options = $item['options'] ?? [];
            $selected = $item['selected_options'] ?? [];
            $correct = $item['correct_options'] ?? [];
        @endphp
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-white mb-2">Câu {{ $index + 1 }}</h3>
            <p class="text-gray-300 mb-3">{{ $item['question_text'] ?? '' }}</p>
            <div class="space-y-2">
                @foreach ($options as $optIndex => $optText)
                    @php
                        $isSelected = in_array($optIndex, $selected, true);
                        $isCorrect = in_array($optIndex, $correct, true);
                    @endphp
                    <div class="flex items-center gap-2 text-sm {{ $isCorrect ? 'text-green-300' : 'text-gray-300' }}">
                        <span class="font-mono">{{ chr(65 + $optIndex) }}.</span>
                        <span>{{ $optText }}</span>
                        @if ($isSelected)
                            <span class="text-purple-300">(Bạn chọn)</span>
                        @endif
                        @if ($isCorrect)
                            <span class="text-green-400">(Đúng)</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
