<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = [
        'course_id',
        'sender_id',
        'message',
    ];

    // GroupMessage belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // GroupMessage belongs to Sender (User)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
