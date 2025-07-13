<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'units',
        'schedule',
        'teacher_id',
    ];

    protected $casts = [
        'units' => 'decimal:1',
    ];

    /**
     * Get the teacher that owns the subject.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function classes()
    {
        return $this->hasMany(ClassSection::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class)->orderBy('order');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class)->orderBy('order');
    }

    public function recitations()
    {
        return $this->hasMany(Recitation::class)->orderBy('order');
    }

    public function projects()
    {
        return $this->hasMany(Project::class)->orderBy('order');
    }
}
