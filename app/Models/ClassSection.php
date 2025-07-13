<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'section',
        'schedule',
        'classroom',
        'student_count',
        'teacher_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_section_student')
                    ->withPivot('enrollment_date', 'status')
                    ->withTimestamps();
    }

    public function activities()
    {
        return $this->subject->activities();
    }

    public function quizzes()
    {
        return $this->subject->quizzes();
    }

    public function exams()
    {
        return $this->subject->exams();
    }

    public function recitations()
    {
        return $this->subject->recitations();
    }

    public function projects()
    {
        return $this->subject->projects();
    }
}
