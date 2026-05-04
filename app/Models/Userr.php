<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userr extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'cnic',
        'role',
        'email_verified',
        'verification_token', // New
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];



    public function vehicles()
{
    return $this->hasMany(Vehicle::class, 'user_id');
}
    // public function vehicles()
    // {
    //     return $this->hasMany(Vehicle::class);
    // }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    // New method for email verification
    public function verifyEmail()
    {
        $this->email_verified = true;
        $this->verification_token = null;
        $this->save();
    }

    // New method to check if email is verified
    public function isEmailVerified()
    {
        return $this->email_verified == true;
    }


// In Userr.php model
public function complaintsFiled()
{
    return $this->hasMany(Complaint::class, 'customer_id');
}

public function complaintsReceived()
{
    return $this->hasMany(Complaint::class, 'provider_id');
}














public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(Message::class, 'receiver_id');
}

public function unreadMessagesCount()
{
    return $this->receivedMessages()->where('is_read', false)->count();
}

public function getAllConversations()
{
    // Get all unique users that this user has chatted with
    $sentUserIds = $this->sentMessages()->select('receiver_id')->distinct()->pluck('receiver_id');
    $receivedUserIds = $this->receivedMessages()->select('sender_id')->distinct()->pluck('sender_id');
    
    $conversationUserIds = $sentUserIds->merge($receivedUserIds)->unique();
    
    return Userr::whereIn('id', $conversationUserIds)->get();
}

public function getLastMessageWithUser($userId)
{
    return Message::conversation($this->id, $userId)->latest()->first();
}

public function getUnreadCountFromUser($userId)
{
    return Message::where('sender_id', $userId)
        ->where('receiver_id', $this->id)
        ->where('is_read', false)
        ->count();
}







}