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
        <h2 class="text-2xl font-bold text-white mb-6">📊 Bảng Điểm Thi</h2>
        @if($quizGrades->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-300">
                    <thead>
                        <tr class="border-b border-yellow-600">
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Điểm Thi</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Khóa Học</th>
                            <th class="px-4 py-3 text-yellow-300 font-semibold">Lớp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizGrades as $grade)
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition">
                                <td class="px-4 py-3">
                                    <span class="text-yellow-300 font-semibold">{{ $grade->score }}/10</span>
                                </td>
                                <td class="px-4 py-3">{{ $grade->course?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $grade->classroom?->name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400">Chưa có điểm thi nào.</p>
        @endif
    </div>
</div>
@endsection