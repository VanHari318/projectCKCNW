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
        <div class="bg-gray-800 border border-purple-600 rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $classroom->name }}</h2>
                    <p class="text-gray-400">Mã: <span class="font-mono text-purple-300">{{ $classroom->code }}</span></p>
                </div>
                <form method="POST" action="{{ route('teacher.classrooms.destroy', $classroom) }}" onsubmit="return confirm('Xóa lớp học?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300">Xóa lớp</button>
                </form>
            </div>

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
        </div>

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

        <div class="bg-gray-800 border border-yellow-600 rounded-lg p-6">
            <h3 class="text-lg font-bold text-white mb-4">Sinh viên chờ duyệt</h3>
            @php
                $pendingStudents = $classroom->students()->wherePivot('status', 'pending')->get();
            @endphp
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
                    <div class="bg-gray-900 p-4 rounded mb-3 border border-gray-700">
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
                    </div>
                @endforeach
            </div>
        @endif

        @php
            $approvedStudents = $classroom->students()->wherePivot('status', 'approved')->get();
        @endphp
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
</script>
@endsection
