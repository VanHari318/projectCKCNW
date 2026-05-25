<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'join_url',
        'scheduled_at',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Room belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
