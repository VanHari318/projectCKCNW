<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'classroom_id',
        'course_id',
        'student_id',
        'gradeable_id',
        'gradeable_type',
        'score',
        'submission_content',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'score' => 'float',
    ];

    // Grade belongs to Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Grade belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Grade belongs to Student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Polymorphic relation: assignment or quiz
    public function gradeable()
    {
        return $this->morphTo();
    }
}
