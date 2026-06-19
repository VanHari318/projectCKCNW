@extends('layouts.app')

@section('title', 'Nhóm chat khóa học')

@section('content')
<div class="max-w-6xl mx-auto bg-gray-900 border border-purple-500/30 rounded-2xl overflow-hidden shadow-2xl flex h-[650px]">
    
    <!-- Sidebar: Danh sách các khóa học đã tham gia -->
    <div class="w-full md:w-80 border-r border-purple-500/20 flex flex-col bg-[#141426]">
        
        <div class="p-4 border-b border-purple-500/20 bg-[#0f0f1e] flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-200 uppercase tracking-wider">Nhóm học phần</h2>
            <span class="text-xs bg-purple-900/60 text-purple-300 font-medium px-2 py-0.5 rounded-full border border-purple-700/30">
                {{ count($courses) }} Nhóm
            </span>
        </div>

        <!-- Course List -->
        <div class="flex-1 overflow-y-auto divide-y divide-purple-500/10 custom-scrollbar">
            @forelse($courses as $course)
                <button 
                    onclick="loadGroupChat({{ $course->id }})" 
                    id="course-item-{{ $course->id }}"
                    class="w-full text-left p-4 flex items-center gap-3 hover:bg-purple-950/20 transition cursor-pointer border-l-4 border-transparent"
                >
                    <div class="w-10 h-10 rounded-full bg-purple-600/30 border border-purple-500/50 flex items-center justify-center text-purple-200 font-bold uppercase text-sm flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A5.998 5.998 0 0 1 1.13 6.07l2.22-1.11a12.016 12.016 0 0 1 17.3 0l2.22 1.11a5.993 5.993 0 0 1 3.508 3.264 50.627 50.627 0 0 0-2.658.813m-15.482 0A50.57 50.57 0 0 1 12 12.75a50.57 50.57 0 0 1 6.741-2.603" />
                        </svg>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <h3 class="text-sm font-semibold text-gray-200 truncate">{{ $course->name }}</h3>
                            <span class="text-[9px] text-gray-500 flex-shrink-0" id="time-course-{{ $course->id }}">{{ $course->last_message_time }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-0.5">
                            <p class="text-xs text-gray-400 truncate flex-1 pr-2" id="last-message-course-{{ $course->id }}">
                                @if($course->last_message)
                                    <span class="text-purple-400 font-medium">{{ $course->last_message_sender == auth()->user()->name ? 'Bạn' : $course->last_message_sender }}: </span>{{ $course->last_message }}
                                @else
                                    <span class="text-gray-500 italic">Chưa có tin nhắn nhóm</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-xs text-gray-500">
                    Bạn chưa tham gia khóa học nào có nhóm chat.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Group Chat Pane -->
    <div class="flex-1 flex flex-col bg-[#1a1a2e] relative" id="chat-pane">
        
        <!-- Welcome Screen -->
        <div id="welcome-screen" class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-[#1a1a2e]">
            <div class="w-20 h-20 rounded-full bg-purple-900/30 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-4 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-200">Nhóm Chat Khóa Học</h2>
            <p class="text-sm text-gray-400 mt-2 max-w-sm">Chọn một nhóm học phần ở bên trái để bắt đầu thảo luận với giáo viên và các học sinh khác trong khóa học.</p>
        </div>

        <!-- Chat Header (Ẩn ban đầu) -->
        <div id="chat-header" class="hidden px-6 py-4 border-b border-purple-500/20 bg-[#0f0f1e] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-700 text-white font-bold flex items-center justify-center uppercase text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-purple-200">
                        <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.34 3.125 3.125 0 0 0-2.51 3.637 53.606 53.606 0 0 1-.707 9.493.75.75 0 0 1-1.218.456 53.864 53.864 0 0 0-4.004-3.418.75.75 0 0 0-.679-.08 54.062 54.062 0 0 1-4.96.945.75.75 0 0 1-.77-.48l-1.024-2.56a.75.75 0 0 0-.82-.472 53.766 53.766 0 0 1-4.707-.468.75.75 0 0 1-.611-.904 53.957 53.957 0 0 0 .19-7.391.75.75 0 0 0-.585-.812 3.125 3.125 0 0 0-2.514-3.638.75.75 0 0 1-.225-1.34 60.64 60.64 0 0 1 10.37-5.916ZM12 6.75a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-200" id="chat-header-course-name">-</h2>
                    <p class="text-[10px] text-purple-400" id="chat-header-class-name">-</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-300 font-semibold block" id="chat-header-members-count">- thành viên</span>
                <span class="text-[10px] text-gray-500 block" id="chat-header-teacher-name">GV: -</span>
            </div>
        </div>

        <!-- Messages Area (Ẩn ban đầu) -->
        <div id="messages-container" class="hidden flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-[#16162a]">
            <!-- Tin nhắn nhóm được tải động -->
        </div>

        <!-- Input Area (Ẩn ban đầu) -->
        <div id="chat-input-area" class="hidden p-4 border-t border-purple-500/20 bg-[#0f0f1e]">
            <form id="chat-form" onsubmit="sendGroupChatMessage(event)" class="flex gap-2 items-center">
                <input 
                    type="text" 
                    id="message-input" 
                    placeholder="Nhập tin nhắn nhóm..." 
                    autocomplete="off"
                    class="flex-1 bg-[#1e1e32] text-gray-200 placeholder-gray-500 text-sm rounded-xl px-4 py-3 border border-purple-500/20 focus:outline-none focus:border-purple-500 transition"
                >
                <button 
                    type="submit" 
                    class="bg-purple-600 hover:bg-purple-500 text-white p-3 rounded-xl transition flex items-center justify-center cursor-pointer shadow-lg hover:shadow-purple-500/20"
                    title="Gửi"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Kiểu thanh cuộn tuỳ chỉnh */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.2);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.4);
    }
    
    .active-chat {
        background-color: rgba(139, 92, 246, 0.1) !important;
        border-left-color: #8b5cf6 !important;
    }
</style>

<script>
    let activeCourseId = null;
    let pollInterval = null;
    const currentUserId = {{ auth()->id() }};

    function loadGroupChat(courseId) {
        // Đặt active class cho item bên sidebar
        document.querySelectorAll('[id^="course-item-"]').forEach(item => {
            item.classList.remove('active-chat');
        });
        const selectedItem = document.getElementById(`course-item-${courseId}`);
        if (selectedItem) {
            selectedItem.classList.add('active-chat');
        }

        activeCourseId = courseId;

        // Hiển thị các khu vực ẩn
        document.getElementById('welcome-screen').classList.add('hidden');
        document.getElementById('chat-header').classList.remove('hidden');
        document.getElementById('messages-container').classList.remove('hidden');
        document.getElementById('chat-input-area').classList.remove('hidden');

        // Tải tin nhắn
        fetchGroupMessages(courseId);

        // Khởi động vòng lặp lấy tin nhắn mới (mỗi 2 giây)
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            if (activeCourseId === courseId) {
                fetchGroupMessagesSilently(courseId);
            }
        }, 2000);
    }

    function fetchGroupMessages(courseId) {
        const container = document.getElementById('messages-container');
        container.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
            </div>
        `;

        fetch(`/group-messages/chats/${courseId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật header
                    document.getElementById('chat-header-course-name').textContent = data.course.name;
                    document.getElementById('chat-header-class-name').textContent = "Lớp: " + data.course.classroom_name;
                    document.getElementById('chat-header-members-count').textContent = data.course.members_count + " thành viên";
                    document.getElementById('chat-header-teacher-name').textContent = "GV: " + data.course.teacher.name;

                    // Vẽ các tin nhắn
                    renderGroupMessages(data.messages);
                }
            })
            .catch(err => {
                console.error("Lỗi lấy tin nhắn nhóm:", err);
                container.innerHTML = `<div class="text-center text-red-400 text-sm py-8">Không thể tải tin nhắn nhóm. Hãy thử lại.</div>`;
            });
    }

    let lastGroupMessagesJson = '';
    function fetchGroupMessagesSilently(courseId) {
        fetch(`/group-messages/chats/${courseId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && activeCourseId === courseId) {
                    const currentJson = JSON.stringify(data.messages);
                    if (currentJson !== lastGroupMessagesJson) {
                        renderGroupMessages(data.messages);
                        lastGroupMessagesJson = currentJson;
                    }
                }
            });
    }

    function renderGroupMessages(messages) {
        const container = document.getElementById('messages-container');
        
        // Lưu trạng thái cuộn để kiểm tra xem user có đang cuộn lên không
        const isAtBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 60;

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-500 text-xs py-12">
                    <p>Chưa có thảo luận nào trong nhóm này.</p>
                    <p class="mt-1">Hãy gửi tin nhắn đầu tiên để bắt đầu buổi thảo luận!</p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.sender_id === currentUserId;
            
            if (isMe) {
                // Tin nhắn của tôi (bên phải, màu tím)
                html += `
                    <div class="flex justify-end items-start gap-2.5">
                        <div class="flex flex-col w-full max-w-[320px] leading-1.5 items-end">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-semibold text-gray-400">Bạn</span>
                                <span class="text-[10px] font-normal text-gray-500">${msg.time_ago}</span>
                            </div>
                            <p class="text-sm font-normal py-2.5 px-4 bg-purple-600 rounded-s-2xl rounded-ee-2xl text-white mt-1 break-words max-w-full">
                                ${msg.message}
                            </p>
                        </div>
                    </div>
                `;
            } else {
                // Tin nhắn của thành viên khác (bên trái, kèm tên & vai trò)
                const isTeacher = msg.sender_role === 'Giáo viên';
                const roleBadge = isTeacher ? 
                    `<span class="text-[9px] bg-red-950/60 text-red-400 font-medium px-1 rounded border border-red-800/20 ml-1">Giáo viên</span>` : 
                    `<span class="text-[9px] bg-blue-950/60 text-blue-400 font-medium px-1 rounded border border-blue-800/20 ml-1">Học sinh</span>`;

                html += `
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-purple-900/40 border border-purple-500/20 text-purple-200 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 mt-2">
                            ${msg.sender_name.substring(0, 1)}
                        </div>
                        <div class="flex flex-col w-full max-w-[320px] leading-1.5">
                            <div class="flex items-center space-x-2 flex-wrap">
                                <span class="text-xs font-semibold text-gray-300">${msg.sender_name}</span>
                                ${roleBadge}
                                <span class="text-[10px] font-normal text-gray-500">${msg.time_ago}</span>
                            </div>
                            <p class="text-sm font-normal py-2.5 px-4 bg-[#23233c] border border-purple-500/10 rounded-e-2xl rounded-es-2xl text-gray-200 mt-1 break-words max-w-full">
                                ${msg.message}
                            </p>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;

        // Nếu lúc đầu ở cuối (hoặc mới mở chat), tự cuộn xuống cuối
        if (isAtBottom || lastGroupMessagesJson === '') {
            container.scrollTop = container.scrollHeight;
        }
    }

    function sendGroupChatMessage(event) {
        event.preventDefault();
        
        const input = document.getElementById('message-input');
        const messageText = input.value.trim();
        
        if (!messageText || !activeCourseId) return;

        input.value = ''; // Xoá ô input ngay lập tức
        
        const formData = new FormData();
        formData.append('message', messageText);

        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                          document.querySelector('meta[name="csrf-token"]').getAttribute('content') : 
                          '{{ csrf_token() }}';

        fetch(`/group-messages/chats/${activeCourseId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Cập nhật lại giao diện chat ngầm
                fetchGroupMessagesSilently(activeCourseId);
                
                // Cập nhật lại sidebar tin nhắn cuối
                const lastMsgLabel = document.getElementById(`last-message-course-${activeCourseId}`);
                if (lastMsgLabel) {
                    lastMsgLabel.innerHTML = `<span class="text-purple-400 font-medium">Bạn: </span>${data.message.message}`;
                }
                const timeLabel = document.getElementById(`time-course-${activeCourseId}`);
                if (timeLabel) {
                    timeLabel.textContent = 'Vừa xong';
                }
            }
        })
        .catch(err => {
            console.error("Lỗi gửi tin nhắn nhóm:", err);
            alert("Có lỗi xảy ra khi gửi tin nhắn nhóm. Vui lòng thử lại!");
            input.value = messageText; // Trả lại text nếu lỗi
        });
    }

    // Tự động mở nhóm đầu tiên nếu có
    document.addEventListener('DOMContentLoaded', function() {
        const firstCourseItem = document.querySelector('[id^="course-item-"]');
        if (firstCourseItem) {
            const firstId = firstCourseItem.id.replace('course-item-', '');
            loadGroupChat(firstId);
        }
    });
</script>
@endsection
