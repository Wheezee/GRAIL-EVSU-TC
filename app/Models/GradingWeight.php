<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'activities',
        'quizzes',
        'exams',
        'recitation',
        'projects',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
} 