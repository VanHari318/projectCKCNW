<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
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

    // Quiz belongs to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Quiz has polymorphic Grades
    public function grades()
    {
        return $this->morphMany(Grade::class, 'gradeable');
    }
}
