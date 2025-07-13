<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'max_score',
        'description',
        'due_date',
        'order',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores()
    {
        return $this->hasMany(ProjectScore::class);
    }

    public function getStudentScore($studentId)
    {
        return $this->scores()->where('student_id', $studentId)->first();
    }

    public function hasDueDate()
    {
        return !is_null($this->due_date);
    }

    public function isOverdue()
    {
        if (!$this->hasDueDate()) {
            return false;
        }
        return $this->due_date->isPast();
    }
}
