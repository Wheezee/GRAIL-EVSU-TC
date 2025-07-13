<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'max_score',
        'description',
        'order',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(QuizScore::class);
    }

    public function getStudentScore($studentId)
    {
        return $this->scores()->where('student_id', $studentId)->first();
    }
}
