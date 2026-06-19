@extends('layouts.app')

@section('title', 'Nguồn Bài kiểm tra')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-white mb-4">Source Bài kiểm tra</h1>

    <div class="bg-gray-800 p-4 rounded mb-4 flex flex-wrap items-center gap-4">
        <div>
            <label class="text-sm text-gray-300">Loại:</label>
            <select id="filterType" class="ml-2 rounded form-input px-3 py-1">
                <option value="assignment">Bài tập</option>
                <option value="quiz" selected>Bài kiểm tra</option>
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-300">Khóa học:</label>
            <select id="filterCourse" class="ml-2 rounded form-input px-3 py-1">
                <option value="all">Tất cả</option>
                @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="listContainer" class="space-y-2">
        <div id="assignmentsList" class="hidden">
            @php
            $assignmentsByCourse = $assignments->groupBy('course_id');
            @endphp
            @forelse($assignmentsByCourse as $courseId => $courseAssignments)
            <div data-course-group data-course-id="{{ $courseId }}" class="mb-4">
                <h3 class="text-lg font-semibold text-purple-300 mb-2 border-b border-gray-700 pb-1">
                    📚 {{ $courseAssignments->first()->course->name ?? 'Khóa #'.$courseId }}
                </h3>
                <div class="space-y-2">
                    @foreach($courseAssignments as $a)
                    <div data-item data-course-id="{{ $courseId }}" class="bg-gray-900 p-3 rounded border border-gray-700">
                        <div class="flex justify-between items-center">
                            <button type="button" class="text-left flex-1 text-white font-medium title-toggle">{{ $a->title }}</button>
                            <div class="ml-4 flex items-center gap-2">
                                <button type="button" class="text-yellow-300 px-2 py-1 rounded repost-btn" data-type="assignment" data-id="{{ $a->id }}" data-title="{{ $a->title }}" data-questions='@json($a->questions)'>Đăng lại</button>
                                <a href="{{ route('teacher.assignments.edit', $a) }}" class="text-purple-300 px-2 py-1 rounded">Sửa</a>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-300 hidden detail-content">
                            @if($a->questions)
                            @foreach($a->questions as $q)
                            <div class="mb-2">
                                <div class="font-semibold">Câu {{ $loop->iteration }}: {{ $q['question_text'] ?? $q->question_text ?? 'N/A' }}</div>
                                @php
                                $opts = $q['options'] ?? ($q->options ?? []);
                                $letters = ['A','B','C','D'];
                                @endphp
                                @if(!empty($opts))
                                <ul class="pl-5">
                                    @foreach($opts as $i => $opt)
                                    <li><span class="font-semibold">{{ $letters[$i] ?? chr(65+$i) }}.</span> {{ $opt }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <p>Không có câu hỏi.</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-gray-400">Chưa có bài tập.</p>
            @endforelse
        </div>

        <div id="quizzesList">
            @php
            $quizzesByCourse = $quizzes->groupBy('course_id');
            @endphp
            @forelse($quizzesByCourse as $courseId => $courseQuizzes)
            <div data-course-group data-course-id="{{ $courseId }}" class="mb-4">
                <h3 class="text-lg font-semibold text-purple-300 mb-2 border-b border-gray-700 pb-1">
                    📚 {{ $courseQuizzes->first()->course->name ?? 'Khóa #'.$courseId }}
                </h3>
                <div class="space-y-2">
                    @foreach($courseQuizzes as $qz)
                    <div data-item data-course-id="{{ $courseId }}" class="bg-gray-900 p-3 rounded border border-gray-700">
                        <div class="flex justify-between items-center">
                            <button type="button" class="text-left flex-1 text-white font-medium title-toggle">{{ $qz->title }}</button>
                            <div class="ml-4 flex items-center gap-2">
                                <button type="button" class="text-yellow-300 px-2 py-1 rounded repost-btn" data-type="quiz" data-id="{{ $qz->id }}" data-title="{{ $qz->title }}" data-questions='@json($qz->questions)'>Đăng lại</button>
                                <a href="{{ route('teacher.quizzes.edit', $qz) }}" class="text-purple-300 px-2 py-1 rounded">Sửa</a>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-300 hidden detail-content">
                            @if($qz->questions)
                            @foreach($qz->questions as $qq)
                            <div class="mb-2">
                                <div class="font-semibold">Câu {{ $loop->iteration }}: {{ $qq['question_text'] ?? $qq->question_text ?? 'N/A' }}</div>
                                @php
                                $opts = $qq['options'] ?? ($qq->options ?? []);
                                $letters = ['A','B','C','D'];
                                @endphp
                                @if(!empty($opts))
                                <ul class="pl-5">
                                    @foreach($opts as $i => $opt)
                                    <li><span class="font-semibold">{{ $letters[$i] ?? chr(65+$i) }}.</span> {{ $opt }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <p>Không có câu hỏi.</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-gray-400">Chưa có bài kiểm tra.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ url()->previous() }}" class="btn-primary px-4 py-2 rounded">Thoát</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterType = document.getElementById('filterType');
        const filterCourse = document.getElementById('filterCourse');
        const assignmentsList = document.getElementById('assignmentsList');
        const quizzesList = document.getElementById('quizzesList');

        function applyFilters() {
            const type = filterType.value;
            const courseId = filterCourse.value;

            // Ẩn/hiện danh sách theo loại
            if (type === 'assignment') {
                assignmentsList.classList.remove('hidden');
                quizzesList.classList.add('hidden');
            } else {
                assignmentsList.classList.add('hidden');
                quizzesList.classList.remove('hidden');
            }

            // Lọc theo khóa học
            const activeList = type === 'assignment' ? assignmentsList : quizzesList;
            activeList.querySelectorAll('[data-course-group]').forEach(group => {
                if (courseId === 'all' || group.dataset.courseId === courseId) {
                    group.classList.remove('hidden');
                } else {
                    group.classList.add('hidden');
                }
            });
        }

        filterType.addEventListener('change', applyFilters);
        filterCourse.addEventListener('change', applyFilters);

        document.querySelectorAll('.title-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const container = this.closest('[data-item]');
                const detail = container ? container.querySelector('.detail-content') : null;
                if (detail) detail.classList.toggle('hidden');
            });
        });
    });
</script>

@endsection

<!-- Repost Modal -->
<div id="repostModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded p-6 w-full max-w-xl">
        <h3 class="text-lg font-bold text-white mb-4">Đăng lại nguồn</h3>
        <form id="repostForm" method="POST" action="{{ route('teacher.quizzes.store') }}">
            @csrf
            <input type="hidden" name="source_id" id="source_id">
            <input type="hidden" name="source_type" id="source_type">
            @if(isset($classroom) && $classroom)
            <input type="hidden" name="classroom_id" id="repostClassroomId" value="{{ $classroom->id }}">
            @endif
            <div class="mb-3">
                <label class="text-sm text-gray-300">Tiêu đề</label>
                <input type="text" id="repostTitle" name="title" class="w-full rounded form-input px-3 py-2" readonly>
            </div>
            <div class="flex flex-col md:flex-row gap-3 mb-3">
                <select name="scope" id="repostScope" class="rounded form-input px-4 py-2">
                    <option value="course">Theo khóa học</option>
                    <option value="classroom">Toàn lớp</option>
                </select>
                <select name="course_id" id="repostCourse" class="rounded form-input px-4 py-2 flex-1">
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <input type="number" name="duration" id="repostDuration" class="w-full rounded form-input px-4 py-2" placeholder="Thời lượng (phút)" min="1" required>
                <input type="datetime-local" name="due_date" id="repostDue" class="w-full rounded form-input px-4 py-2" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="repostCancel" class="px-4 py-2 rounded bg-gray-700">Hủy</button>
                <button type="submit" id="repostSubmit" class="px-4 py-2 rounded btn-primary">Tạo bài kiểm tra</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const repostModal = document.getElementById('repostModal');
        const repostForm = document.getElementById('repostForm');
        const repostTitle = document.getElementById('repostTitle');
        const repostCourse = document.getElementById('repostCourse');
        const repostScope = document.getElementById('repostScope');
        const repostDuration = document.getElementById('repostDuration');
        const repostDue = document.getElementById('repostDue');
        const repostCancel = document.getElementById('repostCancel');
        const sourceIdInput = document.getElementById('source_id');
        const sourceTypeInput = document.getElementById('source_type');

        document.querySelectorAll('.repost-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                const id = this.dataset.id;
                const title = this.dataset.title || '';
                sourceIdInput.value = id;
                sourceTypeInput.value = type;
                repostTitle.value = title;
                repostForm.action = type === 'assignment' ? "{{ route('teacher.assignments.store') }}" : "{{ route('teacher.quizzes.store') }}";
                document.getElementById('repostSubmit').textContent = type === 'assignment' ? 'Giao bài tập' : 'Tạo bài kiểm tra';
                repostModal.classList.remove('hidden');
            });
        });

        repostCancel.addEventListener('click', () => {
            repostModal.classList.add('hidden');
        });
    });
</script>