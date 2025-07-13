<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'max_score',
        'due_date',
        'description',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'max_score' => 'decimal:2',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores()
    {
        return $this->hasMany(ActivityScore::class);
    }

    public function getStudentScore($studentId)
    {
        return $this->scores()->where('student_id', $studentId)->first();
    }

    public function hasDueDate()
    {
        return !is_null($this->due_date);
    }
}
