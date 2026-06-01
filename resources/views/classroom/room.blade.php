@extends('layouts.app')

@section('title', 'Phòng học trực tuyến: ' . $room->title)

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header/Navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-800 p-6 rounded-lg border border-purple-600 shadow-lg">
        <div>
            @if(Auth::user()->isTeacher())
                <a href="{{ route('teacher.manage') }}" class="text-purple-400 hover:text-purple-300 text-sm mb-2 inline-block">
                    ← Quay lại quản lý lớp học
                </a>
            @else
                <a href="{{ route('student.courses.show', $room->course) }}" class="text-purple-400 hover:text-purple-300 text-sm mb-2 inline-block">
                    ← Quay lại khóa học
                </a>
            @endif
            <h1 class="text-2xl font-bold text-white mb-1 flex items-center gap-2">
                <span class="inline-flex items-center justify-center p-1.5 bg-red-500 rounded-full animate-pulse"></span>
                {{ $room->title }}
            </h1>
            <p class="text-gray-400 text-sm">
                Khóa học: <span class="text-purple-400 font-semibold">{{ $room->course->name }}</span> | 
                Lớp: <span class="text-gray-300">{{ $room->course->classroom->name }}</span>
            </p>
        </div>
        <div class="mt-4 md:mt-0 bg-gray-900 border border-gray-700 rounded px-4 py-2 text-sm text-gray-300">
            📅 Bắt đầu: {{ \Carbon\Carbon::parse($room->scheduled_at)->format('H:i - d/m/Y') }}
        </div>
    </div>

    <!-- MiroTalk Container -->
    <div class="bg-gray-900 border border-gray-700 rounded-lg overflow-hidden shadow-2xl" style="height: 75vh;">
        @php
            $joinUrl = $room->join_url;
            
            // Support both old Jitsi URLs and MiroTalk URLs
            if (str_contains($joinUrl, 'mirotalk.com')) {
                $userName = Auth::user()->name . ' (' . (Auth::user()->role === 'teacher' ? 'Giáo viên' : 'Học sinh') . ')';
                $separator = str_contains($joinUrl, '?') ? '&' : '?';
                $embedUrl = $joinUrl . $separator . 'name=' . urlencode($userName);
            } else {
                $embedUrl = $joinUrl;
            }
        @endphp

        @if(str_contains($joinUrl, 'meet.jit.si') || str_contains($joinUrl, 'meet.ffmuc.net'))
            <!-- Fallback support for old Jitsi Meet configuration -->
            <div id="jitsi-meet-container" class="w-full h-full"></div>
            <script src="https://meet.ffmuc.net/external_api.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const joinUrl = "{{ $joinUrl }}";
                    let roomName = "";
                    let domain = "meet.ffmuc.net";
                    try {
                        const urlObj = new URL(joinUrl);
                        roomName = urlObj.pathname.substring(1);
                        domain = urlObj.hostname;
                    } catch (e) {
                        roomName = "ProjectCK_Room_{{ $room->id }}";
                    }
                    const options = {
                        roomName: roomName,
                        width: '100%',
                        height: '100%',
                        parentNode: document.querySelector('#jitsi-meet-container'),
                        userInfo: {
                            displayName: "{{ Auth::user()->name }} ({{ Auth::user()->role === 'teacher' ? 'Giáo viên' : 'Học sinh' }})",
                            email: "{{ Auth::user()->email }}"
                        },
                        configOverwrite: {
                            startWithAudioMuted: true,
                            startWithVideoMuted: true,
                            prejoinPageEnabled: false
                        }
                    };
                    new JitsiMeetExternalAPI(domain, options);
                });
            </script>
        @else
            <!-- Embedded MiroTalk P2P Meeting -->
            <iframe 
                src="{{ $embedUrl }}" 
                class="w-full h-full"
                allow="camera; microphone; speaker-selection; display-capture; fullscreen; clipboard-read; clipboard-write; web-share; autoplay; picture-in-picture"
                style="border: 0;">
            </iframe>
        @endif
    </div>
</div>
@endsection
