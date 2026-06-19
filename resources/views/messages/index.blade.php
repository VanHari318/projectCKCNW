@extends('layouts.app')

@section('title', 'Hộp thư tin nhắn')

@section('content')
<div class="max-w-6xl mx-auto bg-gray-900 border border-purple-500/30 rounded-2xl overflow-hidden shadow-2xl flex h-[650px]">
    
    <!-- Sidebar: Danh sách chat & Tìm kiếm -->
    <div class="w-full md:w-80 border-r border-purple-500/20 flex flex-col bg-[#141426]">
        
        <!-- Search bar -->
        <div class="p-4 border-b border-purple-500/20 bg-[#0f0f1e]">
            <form action="{{ route('messages.index') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Tìm kiếm người dùng..." 
                    value="{{ request('search') }}"
                    class="w-full bg-[#1e1e32] text-gray-200 placeholder-gray-500 text-sm rounded-xl px-4 py-2.5 pr-10 border border-purple-500/20 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                >
                @if(request('search'))
                    <a href="{{ route('messages.index') }}" class="absolute right-9 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Users List -->
        <div class="flex-1 overflow-y-auto divide-y divide-purple-500/10 custom-scrollbar">
            
            <!-- Hiển thị kết quả tìm kiếm nếu có -->
            @if(request('search'))
                <div class="px-4 py-2 text-xs font-semibold text-purple-400 uppercase tracking-wider bg-[#0f0f1e]/50">
                    Kết quả tìm kiếm
                </div>
                @forelse($searchedUsers as $user)
                    <button 
                        onclick="loadChat({{ $user->id }})" 
                        id="user-item-{{ $user->id }}"
                        class="w-full text-left p-4 flex items-center gap-3 hover:bg-purple-950/20 transition cursor-pointer border-l-4 border-transparent"
                    >
                        <div class="w-10 h-10 rounded-full bg-purple-600/30 border border-purple-500/50 flex items-center justify-center text-purple-200 font-bold uppercase text-sm">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-gray-200 truncate">{{ $user->name }}</h3>
                                <span class="text-[10px] text-purple-400 font-medium px-1.5 py-0.5 rounded-full bg-purple-950/40 border border-purple-800/30">
                                    {{ $user->role == 'teacher' ? 'GV' : 'HS' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $user->email }}</p>
                        </div>
                    </button>
                @empty
                    <div class="p-4 text-center text-xs text-gray-500">
                        Không tìm thấy người dùng phù hợp.
                    </div>
                @endforelse
            @endif

            <!-- Danh sách các cuộc hội thoại gần đây -->
            <div class="px-4 py-2 text-xs font-semibold text-purple-400 uppercase tracking-wider bg-[#0f0f1e]/50">
                Gần đây
            </div>

            @forelse($recentUsers as $user)
                <button 
                    onclick="loadChat({{ $user->id }})" 
                    id="user-item-{{ $user->id }}"
                    class="w-full text-left p-4 flex items-center gap-3 hover:bg-purple-950/20 transition cursor-pointer border-l-4 border-transparent active-chat-border"
                >
                    <div class="w-10 h-10 rounded-full bg-purple-700/40 border border-purple-500/40 flex items-center justify-center text-purple-200 font-bold uppercase text-sm relative flex-shrink-0">
                        {{ substr($user->name, 0, 1) }}
                        @if($user->unread_count > 0)
                            <span id="badge-user-{{ $user->id }}" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold h-5 w-5 rounded-full flex items-center justify-center shadow-lg border border-[#141426]">
                                {{ $user->unread_count }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline">
                            <h3 class="text-sm font-semibold text-gray-200 truncate">{{ $user->name }}</h3>
                            <span class="text-[10px] text-gray-500 flex-shrink-0" id="time-user-{{ $user->id }}">{{ $user->last_message_time }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-0.5">
                            <p class="text-xs text-gray-400 truncate flex-1 pr-2" id="last-message-user-{{ $user->id }}">
                                @if($user->last_message_sender_id == auth()->id())
                                    <span class="text-purple-400">Bạn: </span>
                                @endif
                                {{ $user->last_message }}
                            </p>
                            @if($user->unread_count > 0)
                                <div class="w-2 h-2 rounded-full bg-purple-500 flex-shrink-0" id="dot-user-{{ $user->id }}"></div>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-xs text-gray-500">
                    Chưa có cuộc hội thoại nào.<br>Hãy tìm kiếm người dùng phía trên để bắt đầu nhắn tin.
                </div>
            @endforelse

        </div>
    </div>

    <!-- Chat Pane -->
    <div class="flex-1 flex flex-col bg-[#1a1a2e] relative" id="chat-pane">
        
        <!-- Welcome Screen -->
        <div id="welcome-screen" class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-[#1a1a2e]">
            <div class="w-20 h-20 rounded-full bg-purple-900/30 border border-purple-500/20 flex items-center justify-center text-purple-400 mb-4 animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-200">Trò chuyện 1-1</h2>
            <p class="text-sm text-gray-400 mt-2 max-w-sm">Chọn một người dùng bên trái để xem cuộc trò chuyện hoặc bắt đầu cuộc trò chuyện mới.</p>
        </div>

        <!-- Chat Header (Ẩn ban đầu) -->
        <div id="chat-header" class="hidden px-6 py-4 border-b border-purple-500/20 bg-[#0f0f1e] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-700 text-white font-bold flex items-center justify-center uppercase text-sm" id="chat-header-avatar">
                    -
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-200" id="chat-header-name">-</h2>
                    <p class="text-[10px] text-purple-400" id="chat-header-role">-</p>
                </div>
            </div>
            <div class="text-gray-400 hover:text-white text-xs">
                <span id="chat-header-email" class="opacity-80"></span>
            </div>
        </div>

        <!-- Messages Area (Ẩn ban đầu) -->
        <div id="messages-container" class="hidden flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-[#16162a]">
            <!-- Tin nhắn sẽ được chèn động qua JavaScript -->
        </div>

        <!-- Input Area (Ẩn ban đầu) -->
        <div id="chat-input-area" class="hidden p-4 border-t border-purple-500/20 bg-[#0f0f1e]">
            <form id="chat-form" onsubmit="sendChatMessage(event)" class="flex gap-2 items-center">
                <input 
                    type="text" 
                    id="message-input" 
                    placeholder="Nhập tin nhắn..." 
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
    let activeContactId = null;
    let pollInterval = null;
    const currentUserId = {{ auth()->id() }};

    function loadChat(contactId) {
        // Đặt active class cho item bên sidebar
        document.querySelectorAll('[id^="user-item-"]').forEach(item => {
            item.classList.remove('active-chat');
        });
        const selectedItem = document.getElementById(`user-item-${contactId}`);
        if (selectedItem) {
            selectedItem.classList.add('active-chat');
        }

        // Xóa badge và dot chưa đọc ở sidebar ngay lập tức
        const badge = document.getElementById(`badge-user-${contactId}`);
        if (badge) badge.remove();
        const dot = document.getElementById(`dot-user-${contactId}`);
        if (dot) dot.remove();

        activeContactId = contactId;

        // Hiện thị các khu vực ẩn
        document.getElementById('welcome-screen').classList.add('hidden');
        document.getElementById('chat-header').classList.remove('hidden');
        document.getElementById('messages-container').classList.remove('hidden');
        document.getElementById('chat-input-area').classList.remove('hidden');

        // Tải tin nhắn
        fetchMessages(contactId);

        // Khởi động vòng lặp lấy tin nhắn mới (mỗi 2 giây)
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            if (activeContactId === contactId) {
                fetchMessagesSilently(contactId);
            }
        }, 2000);
    }

    function fetchMessages(contactId) {
        const container = document.getElementById('messages-container');
        container.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
            </div>
        `;

        fetch(`/messages/chats/${contactId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật header
                    document.getElementById('chat-header-name').textContent = data.contact.name;
                    document.getElementById('chat-header-role').textContent = data.contact.role;
                    document.getElementById('chat-header-avatar').textContent = data.contact.name.substring(0, 1);
                    document.getElementById('chat-header-email').textContent = data.contact.email;

                    // Vẽ các tin nhắn
                    renderMessages(data.messages);
                    
                    // Cập nhật lại số lượng tin nhắn chưa đọc trên navbar
                    if (typeof updateUnreadMessageCount === 'function') {
                        updateUnreadMessageCount();
                    }
                }
            })
            .catch(err => {
                console.error("Lỗi lấy tin nhắn:", err);
                container.innerHTML = `<div class="text-center text-red-400 text-sm py-8">Không thể tải tin nhắn. Hãy thử lại.</div>`;
            });
    }

    // Tải tin nhắn ngầm (không xoay vòng tròn load)
    let lastMessagesJson = '';
    function fetchMessagesSilently(contactId) {
        fetch(`/messages/chats/${contactId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && activeContactId === contactId) {
                    const currentJson = JSON.stringify(data.messages);
                    if (currentJson !== lastMessagesJson) {
                        renderMessages(data.messages);
                        lastMessagesJson = currentJson;
                    }
                }
            });
    }

    function renderMessages(messages) {
        const container = document.getElementById('messages-container');
        
        // Lưu trạng thái cuộn để kiểm tra xem user có đang cuộn lên không
        const isAtBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 60;

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-500 text-xs py-12">
                    <p>Chưa có tin nhắn nào giữa hai bạn.</p>
                    <p class="mt-1">Hãy gửi tin nhắn đầu tiên để bắt đầu cuộc trò chuyện!</p>
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
                // Tin nhắn đối phương (bên trái, màu xám)
                html += `
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-purple-900/40 border border-purple-500/20 text-purple-200 flex items-center justify-center font-bold text-xs uppercase flex-shrink-0 mt-2">
                            ${document.getElementById('chat-header-avatar').textContent}
                        </div>
                        <div class="flex flex-col w-full max-w-[320px] leading-1.5">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-semibold text-gray-300">${document.getElementById('chat-header-name').textContent}</span>
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
        if (isAtBottom || lastMessagesJson === '') {
            container.scrollTop = container.scrollHeight;
        }
    }

    function sendChatMessage(event) {
        event.preventDefault();
        
        const input = document.getElementById('message-input');
        const messageText = input.value.trim();
        
        if (!messageText || !activeContactId) return;

        input.value = ''; // Xoá ô input ngay lập tức để tạo cảm giác mượt mà
        
        const formData = new FormData();
        formData.append('message', messageText);

        // Thêm CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                          document.querySelector('meta[name="csrf-token"]').getAttribute('content') : 
                          '{{ csrf_token() }}';

        fetch(`/messages/chats/${activeContactId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Tải lại tin nhắn ngay để hiển thị tin nhắn vừa gửi
                fetchMessagesSilently(activeContactId);
                
                // Cập nhật lại sidebar
                const lastMsgLabel = document.getElementById(`last-message-user-${activeContactId}`);
                if (lastMsgLabel) {
                    lastMsgLabel.innerHTML = `<span class="text-purple-400">Bạn: </span>${data.message.message}`;
                }
                const timeLabel = document.getElementById(`time-user-${activeContactId}`);
                if (timeLabel) {
                    timeLabel.textContent = 'Vừa xong';
                }
            }
        })
        .catch(err => {
            console.error("Lỗi gửi tin nhắn:", err);
            alert("Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại!");
            input.value = messageText; // Trả lại text nếu lỗi
        });
    }

    // Auto-open chat if a search was performed and we click direct link or if we have users
    // Bắt đầu chat với user đầu tiên trong danh sách nếu có
    document.addEventListener('DOMContentLoaded', function() {
        const firstUserItem = document.querySelector('[id^="user-item-"]');
        if (firstUserItem) {
            const firstId = firstUserItem.id.replace('user-item-', '');
            loadChat(firstId);
        }
    });
</script>
@endsection
