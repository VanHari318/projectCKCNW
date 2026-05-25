<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'duration',
        'due_date',
        'questions',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'questions' => 'array',
    ];

    // Assignment belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Assignment has polymorphic Grades
    public function grades()
    {
        return $this->morphMany(Grade::class, 'gradeable');
    }
}
