<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'name',
        'code',
        'teacher_id',
    ];

    // Classroom belongs to a Teacher (User)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Classroom has many Courses
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Classroom has many Students (Users)
    public function students()
    {
        return $this->belongsToMany(User::class, 'classroom_student', 'classroom_id', 'student_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
