<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'description',
        'classroom_id',
    ];

    // Course belongs to Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Course has many Students (Users)
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withTimestamps();
    }

    // Course has many Rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Course has many Assignments
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Course has many Quizzes
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Course has many Grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Course has many Group Messages
    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class, 'course_id');
    }
}
