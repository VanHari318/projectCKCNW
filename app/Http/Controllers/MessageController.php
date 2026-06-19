<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Show the main messaging dashboard.
     */
    public function index(Request $request)
    {
        $authUserId = Auth::id();
        
        // 1. Get recent users who have exchanged messages with the current user
        $recentMessages = Message::where('sender_id', $authUserId)
            ->orWhere('receiver_id', $authUserId)
            ->orderBy('created_at', 'desc')
            ->get();

        $recentUserIds = collect();
        foreach ($recentMessages as $msg) {
            $targetId = ($msg->sender_id == $authUserId) ? $msg->receiver_id : $msg->sender_id;
            if (!$recentUserIds->contains($targetId)) {
                $recentUserIds->push($targetId);
            }
        }

        $recentUsers = User::whereIn('id', $recentUserIds)
            ->get()
            ->sortBy(function ($user) use ($recentUserIds) {
                return $recentUserIds->search($user->id);
            })
            ->values();

        // Attach last message and unread count for each recent user
        foreach ($recentUsers as $user) {
            $lastMsg = Message::where(function($q) use ($authUserId, $user) {
                $q->where('sender_id', $authUserId)->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($authUserId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authUserId);
            })->orderBy('created_at', 'desc')->first();

            $user->last_message = $lastMsg ? $lastMsg->message : '';
            $user->last_message_time = $lastMsg ? $lastMsg->created_at->diffForHumans() : '';
            $user->last_message_sender_id = $lastMsg ? $lastMsg->sender_id : null;
            
            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $authUserId)
                ->where('is_read', false)
                ->count();
        }

        // 2. Process search if request has a query
        $search = $request->input('search');
        $searchedUsers = collect();
        if (!empty($search)) {
            $searchedUsers = User::where('id', '!=', $authUserId)
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->limit(15)
                ->get();
        }

        return view('messages.index', [
            'recentUsers' => $recentUsers,
            'searchedUsers' => $searchedUsers,
            'search' => $search
        ]);
    }

    /**
     * Get message history with a specific user.
     */
    public function getMessages(User $user)
    {
        $authUserId = Auth::id();

        // Mark messages from this user to me as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $authUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch all messages between these two users
        $messages = Message::where(function($q) use ($authUserId, $user) {
                $q->where('sender_id', $authUserId)->where('receiver_id', $user->id);
            })
            ->orWhere(function($q) use ($authUserId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'receiver_id' => $msg->receiver_id,
                    'message' => e($msg->message),
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->format('H:i d/m/Y'),
                    'time_ago' => $msg->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'status' => 'success',
            'contact' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role == 'teacher' ? 'Giáo viên' : 'Học sinh'
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Send a message to a specific user.
     */
    public function sendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->input('message'),
            'is_read' => false
        ]);

        return response()->json([
            'status' => 'success',
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => e($message->message),
                'created_at' => $message->created_at->format('H:i d/m/Y'),
                'time_ago' => $message->created_at->diffForHumans()
            ]
        ]);
    }

    /**
     * Get total unread messages count for navbar notification.
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }
}
