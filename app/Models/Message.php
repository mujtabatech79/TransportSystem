<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'booking_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'read_at',
        'type'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $attributes = [
        'is_read' => false,
        'type' => 'text'
    ];
    
    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(Userr::class, 'sender_id');
    }
    
    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(Userr::class, 'receiver_id');
    }
    
    /**
     * Get the booking associated with this message
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
    /**
     * Scope for unread messages
     */
    public function scopeUnread($query, $userId)
    {
        return $query->where('receiver_id', $userId)->where('is_read', false);
    }
    
    /**
     * Scope for conversation between two users
     */
    public function scopeConversation($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user1)->where('receiver_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user2)->where('receiver_id', $user1);
        });
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }
        return $this;
    }
    
    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}