@extends('layouts.app')

@section('title', 'Thông tin học sinh')

@section('content')
<div class="flex flex-col gap-8">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Xem thông tin <span class="text-purple-500">bảng điểm</span></h1>
    </div>

    <div class="bg-gray-800 border border-blue-600 rounded-lg p-6">
        <p class="text-gray-400 text-sm">Điểm thi trung bình</p>
        <p class="text-3xl font-bold text-blue-300">{{ $averageQuizScore !== null ? number_format($averageQuizScore, 2) : 'N/A' }}</p>
    </div>

    <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
        <h2 class="text-2xl font-bold text-white mb-6">📊 Bảng Điểm Thi / Bài Tập</h2>
        @if($allGrades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-300">
                    <thead>
                        <tr class="border-b border-yellow-600">
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Bài tập/Bài kiểm tra</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Điểm</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Khóa Học</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Lớp</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Lịch Sử</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allGrades as $item)
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item['grade']->gradeable_type === 'App\\Models\\Assignment' ? 'bg-blue-900 text-blue-200' : 'bg-yellow-900 text-yellow-200' }}">
                                        {{ $item['type_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-yellow-300 font-semibold">{{ $item['grade']->score }}/10</span>
                                </td>
                                <td class="px-4 py-3">{{ $item['grade']->course?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $item['grade']->classroom?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ $item['review_url'] }}" class="text-purple-300 hover:text-purple-200 text-sm font-semibold">
                                        Lịch sử làm bài→
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400">Chưa có thông tin điểm số.</p>
        @endif
    </div>
</div>
@endsection