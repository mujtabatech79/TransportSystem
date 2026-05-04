<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Userr;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get all conversations for the logged-in user
     */
    public function getConversations()
    {
        $userId = session('user_id');
        $role = session('role');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        $user = Userr::find($userId);
        
        // Get all users this user has chatted with
        $conversations = [];
        
        // Get unique conversation partners
        $sentMessages = Message::where('sender_id', $userId)
            ->select('receiver_id', DB::raw('MAX(created_at) as last_message_time'))
            ->groupBy('receiver_id');
            
        $receivedMessages = Message::where('receiver_id', $userId)
            ->select('sender_id', DB::raw('MAX(created_at) as last_message_time'))
            ->groupBy('sender_id');
            
        // Combine and get unique users
        $allConversations = Message::where(function($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->select(DB::raw('CASE 
                WHEN sender_id = ' . $userId . ' THEN receiver_id 
                ELSE sender_id 
            END as other_user_id'), DB::raw('MAX(created_at) as last_message_time'))
            ->groupBy('other_user_id')
            ->orderBy('last_message_time', 'desc')
            ->get();
            
        foreach ($allConversations as $conv) {
            $otherUser = Userr::find($conv->other_user_id);
            if ($otherUser) {
                $lastMessage = Message::conversation($userId, $otherUser->id)->latest()->first();
                $unreadCount = Message::where('sender_id', $otherUser->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();
                    
                // Get booking info if exists
                $booking = null;
                if ($lastMessage && $lastMessage->booking_id) {
                    $booking = Booking::with('vehicle')->find($lastMessage->booking_id);
                }
                    
                $conversations[] = [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'booking' => $booking
                ];
            }
        }
        
        if ($role == 'customer') {
            return view('customer.messages', compact('conversations'));
        } else {
            return view('provider.messages', compact('conversations'));
        }
    }
    
    /**
     * Get messages for a specific conversation
     */
    public function getMessages($userId)
    {
        $currentUserId = session('user_id');
        
        if (!$currentUserId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $otherUser = Userr::find($userId);
        if (!$otherUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Get all messages between the two users
        $messages = Message::conversation($currentUserId, $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
            
        // Format messages for response
        $formattedMessages = $messages->map(function($message) use ($currentUserId) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'is_mine' => $message->sender_id == $currentUserId,
                'sender_name' => $message->sender->name,
                'time' => $message->created_at->format('h:i A'),
                'date' => $message->created_at->format('M d, Y'),
                'time_ago' => $message->time_ago,
                'is_read' => $message->is_read,
                'booking_id' => $message->booking_id
            ];
        });
        
        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
            'other_user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'role' => $otherUser->role
            ]
        ]);
    }
    
    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:userrs,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $senderId = session('user_id');
        
        if (!$senderId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'booking_id' => $request->booking_id ?? null,
            'type' => 'text'
        ]);
        
        $message->load(['sender', 'receiver']);
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_mine' => true,
                'sender_name' => $message->sender->name,
                'time' => $message->created_at->format('h:i A'),
                'date' => $message->created_at->format('M d, Y'),
                'time_ago' => $message->time_ago,
                'is_read' => $message->is_read,
                'booking_id' => $message->booking_id
            ]
        ]);
    }
    
    /**
     * Start a conversation from booking (called when clicking contract button)
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'vehicle_owner_id' => 'required|exists:userrs,id',
            'booking_id' => 'nullable|exists:bookings,id'
        ]);
        
        $customerId = session('user_id');
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        // Check if conversation already exists, if not, send a system message
        $existingMessage = Message::conversation($customerId, $request->vehicle_owner_id)->first();
        
        if (!$existingMessage) {
            // Send an initial message to start the conversation
            Message::create([
                'sender_id' => $customerId,
                'receiver_id' => $request->vehicle_owner_id,
                'message' => "Hello! I'm interested in your vehicle. Let's discuss the details.",
                'booking_id' => $request->booking_id,
                'type' => 'text'
            ]);
        }
        
        return redirect()->route('messages.conversations')->with('success', 'Conversation started!');
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return response()->json(['count' => 0]);
        }
        
        $count = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}