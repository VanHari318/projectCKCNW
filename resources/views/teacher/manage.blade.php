@extends('layouts.app')

@section('title', 'Quản lý lớp học')

@section('content')
<div class="flex flex-col gap-8">
    <div>
        <h1 class="text-3xl font-bold text-white mb-2">Quản lý <span class="text-purple-500">Lớp Học</span></h1>
        <p class="text-gray-400">Tạo và quản lý lớp học của bạn</p>
    </div>

    @if(!$classroom)
        <div class="bg-gray-800 border border-purple-600 rounded-lg p-6 max-w-2xl">
            <h2 class="text-xl font-bold text-white mb-4">Tạo lớp học mới</h2>
            <form method="POST" action="{{ route('teacher.classrooms.store') }}" class="space-y-4">
                @csrf
                <input type="text" name="name" class="w-full rounded form-input px-4 py-2" placeholder="Tên lớp học" required>
                <button type="submit" class="btn-primary px-4 py-2 rounded w-full">Tạo lớp</button>
            </form>
        </div>
    @else
        <details class="bg-gray-800 border border-purple-600 rounded-lg p-6" id="classroomDetails">
            <summary class="cursor-pointer list-none flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $classroom->name }}</h2>
                    <p class="text-gray-400">Mã: <span class="font-mono text-purple-300">{{ $classroom->code }}</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" class="btn-primary px-3 py-1 rounded" id="toggleClassroom">Xem chi tiết</button>
                    <form method="POST" action="{{ route('teacher.classrooms.destroy', $classroom) }}" onsubmit="return confirm('Xóa lớp học?')" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300">Xóa lớp</button>
                    </form>
                </div>
            </summary>
            <p class="text-gray-400 text-sm mt-2">Bấm "Xem chi tiết" để hiện phần tạo khóa học, phòng học và bài tập.</p>

            <div class="mt-6 flex flex-col gap-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-900 p-3 rounded border border-gray-700">
                        <p class="text-gray-400 text-sm">Sinh viên</p>
                        <p class="text-2xl font-bold text-green-400">{{ $classroom->students->count() }}</p>
                    </div>
                    <div class="bg-gray-900 p-3 rounded border border-gray-700">
                        <p class="text-gray-400 text-sm">Khóa học</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $classroom->courses->count() }}</p>
                    </div>
                    <div class="bg-gray-900 p-3 rounded border border-gray-700">
                        <p class="text-gray-400 text-sm">Phòng học</p>
                        <p class="text-2xl font-bold text-purple-400">{{ $classroom->courses->sum(fn($c) => $c->rooms->count()) }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 justify-end">
                    <button type="button" class="btn-primary px-4 py-2 rounded" id="toggleCourseRoom">
                        + Tạo khóa học / phòng học
                    </button>
                    <button type="button" class="btn-primary px-4 py-2 rounded" id="toggleQuestionPanel">
                        + Tạo bài tập / bài kiểm tra
                    </button>
                </div>

                <div class="flex flex-col gap-6 hidden" id="courseRoomPanel">
                    <div class="bg-gray-800 border border-blue-600 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Tạo khóa học</h3>
                        <form method="POST" action="{{ route('teacher.courses.store', $classroom) }}" class="space-y-3">
                            @csrf
                            <input type="text" name="name" class="w-full rounded form-input px-4 py-2" placeholder="Tên khóa học" required>
                            <textarea name="description" rows="2" class="w-full rounded form-input px-4 py-2" placeholder="Mô tả (tùy chọn)"></textarea>
                            <button type="submit" class="btn-primary px-4 py-2 rounded w-full">Tạo khóa học</button>
                        </form>
                    </div>

                    @if($classroom->courses->count() > 0)
                        <div class="bg-gray-800 border border-green-600 rounded-lg p-6">
                            <h3 class="text-xl font-bold text-white mb-4">Tạo phòng học</h3>
                            <form method="POST" action="{{ route('teacher.rooms.store', $classroom->courses->first()) }}" class="space-y-3" id="roomForm">
                                @csrf
                                <select name="course_id" class="w-full rounded form-input px-4 py-2" onchange="updateRoomForm(this.value)">
                                    @foreach($classroom->courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="title" class="w-full rounded form-input px-4 py-2" placeholder="Tiêu đề phòng" required>
                                <input type="url" name="join_url" class="w-full rounded form-input px-4 py-2" placeholder="Link Zoom/Teams" required>
                                <input type="datetime-local" name="scheduled_at" class="w-full rounded form-input px-4 py-2" required>
                                <button type="submit" class="btn-primary px-4 py-2 rounded w-full">Tạo phòng</button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-800 border border-pink-600 rounded-lg p-6 hidden" id="questionPanel">
                    <h3 class="text-xl font-bold text-white mb-4">Tạo bài tập / bài kiểm tra</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <form method="POST" action="{{ route('teacher.assignments.store') }}" class="space-y-4" data-question-form>
                            @csrf
                            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                            <div class="flex flex-col md:flex-row gap-3">
                                <select name="scope" class="rounded form-input px-4 py-2" data-scope-select>
                                    <option value="course">Theo khóa học</option>
                                    <option value="classroom">Toàn lớp</option>
                                </select>
                                <select name="course_id" class="rounded form-input px-4 py-2 flex-1" data-course-select>
                                    @foreach($classroom->courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="text" name="title" class="w-full rounded form-input px-4 py-2" placeholder="Tên bài tập" required>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="number" name="duration" class="w-full rounded form-input px-4 py-2" placeholder="Thời lượng (phút)" min="1" required>
                                <input type="datetime-local" name="due_date" class="w-full rounded form-input px-4 py-2" required>
                            </div>

                            <div class="space-y-3" data-question-list></div>
                            <div class="flex gap-3">
                                <button type="button" class="btn-primary px-4 py-2 rounded" data-add-question>+ Thêm câu hỏi</button>
                                <button type="submit" class="btn-primary px-4 py-2 rounded">Giao bài tập</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('teacher.quizzes.store') }}" class="space-y-4" data-question-form>
                            @csrf
                            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                            <div class="flex flex-col md:flex-row gap-3">
                                <select name="scope" class="rounded form-input px-4 py-2" data-scope-select>
                                    <option value="course">Theo khóa học</option>
                                    <option value="classroom">Toàn lớp</option>
                                </select>
                                <select name="course_id" class="rounded form-input px-4 py-2 flex-1" data-course-select>
                                    @foreach($classroom->courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="text" name="title" class="w-full rounded form-input px-4 py-2" placeholder="Tên bài kiểm tra" required>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="number" name="duration" class="w-full rounded form-input px-4 py-2" placeholder="Thời lượng (phút)" min="1" required>
                                <input type="datetime-local" name="due_date" class="w-full rounded form-input px-4 py-2" required>
                            </div>

                            <div class="space-y-3" data-question-list></div>
                            <div class="flex gap-3">
                                <button type="button" class="btn-primary px-4 py-2 rounded" data-add-question>+ Thêm câu hỏi</button>
                                <button type="submit" class="btn-primary px-4 py-2 rounded">Tạo bài kiểm tra</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-white mb-4">Sinh viên chờ duyệt</h3>
                    @forelse($pendingStudents as $student)
                        <div class="flex justify-between items-center bg-gray-900 p-3 rounded mb-2 border border-gray-700">
                            <div>
                                <p class="text-white">{{ $student->name }}</p>
                                <p class="text-sm text-gray-400">{{ $student->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('teacher.students.approve', ['classroom' => $classroom, 'student' => $student]) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="text-green-400 hover:text-green-300 text-sm">✓ Duyệt</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-400">Không có sinh viên chờ duyệt.</p>
                    @endforelse
                </div>

                @if($classroom->courses->count() > 0)
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Khóa học</h3>
                        @foreach($classroom->courses as $course)
                            <details class="bg-gray-900 p-4 rounded mb-3 border border-gray-700">
                                <summary class="cursor-pointer list-none">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="text-white font-bold">{{ $course->name }}</p>
                                        <p class="text-sm text-gray-400">{{ $course->description ?? 'Không có mô tả' }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" onsubmit="return confirm('Xóa khóa học?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Xóa</button>
                                    </form>
                                </div>
                                <p class="text-xs text-gray-400">Bấm để xem bài tập và bài kiểm tra</p>
                                </summary>

                                @if($course->rooms->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($course->rooms as $room)
                                            <div class="flex justify-between items-center bg-gray-800 p-2 rounded text-sm border border-gray-600">
                                                <div>
                                                    <p class="text-white">{{ $room->title }}</p>
                                                    <p class="text-xs text-gray-400">{{ $room->scheduled_at ? $room->scheduled_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                                </div>
                                                <form method="POST" action="{{ route('teacher.rooms.destroy', $room) }}" onsubmit="return confirm('Xóa phòng?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300">✕</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-800 border border-gray-700 rounded p-3">
                                        <h4 class="text-sm font-bold text-white mb-2">Bài tập</h4>
                                        @forelse($course->assignments as $assignment)
                                            <details class="bg-gray-900 border border-gray-700 rounded p-2 mb-2">
                                                <summary class="cursor-pointer list-none">
                                                    <div class="flex justify-between items-center text-sm text-gray-300">
                                                        <span>{{ $assignment->title }}</span>
                                                        <div class="flex items-center gap-2">
                                                            <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="text-purple-300 hover:text-purple-200">Sửa</a>
                                                            <form method="POST" action="{{ route('teacher.assignments.destroy', $assignment) }}" onsubmit="return confirm('Xóa bài tập?')" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-400 hover:text-red-300">Xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1">Bấm để xem điểm và thời gian nộp</p>
                                                </summary>

                                                @php
                                                    $assignmentGrades = $assignment->grades->sortBy(fn($g) => $g->student?->name ?? '');
                                                @endphp
                                                @if($assignmentGrades->count() > 0)
                                                    <div class="mt-2 overflow-x-auto">
                                                        <table class="w-full text-xs text-gray-300">
                                                            <thead>
                                                                <tr class="text-left text-gray-400">
                                                                    <th class="py-1">Sinh viên</th>
                                                                    <th class="py-1">Điểm</th>
                                                                    <th class="py-1">Thời gian nộp</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($assignmentGrades as $grade)
                                                                    <tr class="border-t border-gray-700">
                                                                        <td class="py-1">{{ $grade->student?->name ?? 'N/A' }}</td>
                                                                        <td class="py-1">{{ $grade->score }}</td>
                                                                        <td class="py-1">{{ $grade->submitted_at ? $grade->submitted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-400 mt-2">Chưa có sinh viên nộp bài.</p>
                                                @endif
                                            </details>
                                        @empty
                                            <p class="text-xs text-gray-400">Chưa có bài tập.</p>
                                        @endforelse
                                    </div>
                                    <div class="bg-gray-800 border border-gray-700 rounded p-3">
                                        <h4 class="text-sm font-bold text-white mb-2">Bài kiểm tra</h4>
                                        @forelse($course->quizzes as $quiz)
                                            <details class="bg-gray-900 border border-gray-700 rounded p-2 mb-2">
                                                <summary class="cursor-pointer list-none">
                                                    <div class="flex justify-between items-center text-sm text-gray-300">
                                                        <span>{{ $quiz->title }}</span>
                                                        <div class="flex items-center gap-2">
                                                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="text-purple-300 hover:text-purple-200">Sửa</a>
                                                            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Xóa bài kiểm tra?')" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-400 hover:text-red-300">Xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1">Bấm để xem điểm và thời gian nộp</p>
                                                </summary>

                                                @php
                                                    $quizGrades = $quiz->grades->sortBy(fn($g) => $g->student?->name ?? '');
                                                @endphp
                                                @if($quizGrades->count() > 0)
                                                    <div class="mt-2 overflow-x-auto">
                                                        <table class="w-full text-xs text-gray-300">
                                                            <thead>
                                                                <tr class="text-left text-gray-400">
                                                                    <th class="py-1">Sinh viên</th>
                                                                    <th class="py-1">Điểm</th>
                                                                    <th class="py-1">Thời gian nộp</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($quizGrades as $grade)
                                                                    <tr class="border-t border-gray-700">
                                                                        <td class="py-1">{{ $grade->student?->name ?? 'N/A' }}</td>
                                                                        <td class="py-1">{{ $grade->score }}</td>
                                                                        <td class="py-1">{{ $grade->submitted_at ? $grade->submitted_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-400 mt-2">Chưa có sinh viên nộp bài.</p>
                                                @endif
                                            </details>
                                        @empty
                                            <p class="text-xs text-gray-400">Chưa có bài kiểm tra.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif

                @if($approvedStudents->count() > 0)
                    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Sinh viên ({{ $approvedStudents->count() }})</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @foreach($approvedStudents as $student)
                                <div class="bg-gray-900 p-3 rounded border border-gray-700 flex justify-between">
                                    <div>
                                        <p class="text-white font-medium text-sm">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $student->email }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('teacher.students.remove', ['classroom' => $classroom, 'student' => $student]) }}" onsubmit="return confirm('Xóa sinh viên?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs">✕</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </details>
    @endif
</div>

<script>
function updateRoomForm(courseId) {
    const form = document.getElementById('roomForm');
    if (form && courseId) {
        const route = "{{ route('teacher.rooms.store', '__ID__') }}".replace('__ID__', courseId);
        form.action = route;
    }
}

function buildQuestionElement(index) {
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

function renumberQuestions(list) {
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

document.querySelectorAll('[data-question-form]').forEach((form) => {
    const list = form.querySelector('[data-question-list]');
    const addButton = form.querySelector('[data-add-question]');
    const scopeSelect = form.querySelector('[data-scope-select]');
    const courseSelect = form.querySelector('[data-course-select]');

    if (list && list.children.length === 0) {
        list.appendChild(buildQuestionElement(0));
    }

    addButton?.addEventListener('click', () => {
        list.appendChild(buildQuestionElement(list.querySelectorAll('[data-question]').length));
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
        renumberQuestions(list);
    });

    scopeSelect?.addEventListener('change', () => {
        const isClassroom = scopeSelect.value === 'classroom';
        if (courseSelect) {
            courseSelect.disabled = isClassroom;
        }
    });

    if (scopeSelect && courseSelect) {
        courseSelect.disabled = scopeSelect.value === 'classroom';
    }
});

const toggleCourseRoom = document.getElementById('toggleCourseRoom');
const courseRoomPanel = document.getElementById('courseRoomPanel');
const toggleQuestionPanel = document.getElementById('toggleQuestionPanel');
const questionPanel = document.getElementById('questionPanel');
const toggleClassroom = document.getElementById('toggleClassroom');
const classroomDetails = document.getElementById('classroomDetails');

if (toggleCourseRoom && courseRoomPanel) {
    toggleCourseRoom.addEventListener('click', () => {
        courseRoomPanel.classList.toggle('hidden');
    });
}

if (toggleQuestionPanel && questionPanel) {
    toggleQuestionPanel.addEventListener('click', () => {
        questionPanel.classList.toggle('hidden');
    });
}

if (toggleClassroom && classroomDetails) {
    toggleClassroom.addEventListener('click', () => {
        classroomDetails.open = !classroomDetails.open;
    });
}
</script>
@endsection
