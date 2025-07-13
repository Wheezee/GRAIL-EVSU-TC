<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recitation extends Model
{
    use HasFactory;

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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores()
    {
        return $this->hasMany(RecitationScore::class);
    }

    public function getStudentScore($studentId)
    {
        return $this->scores()->where('student_id', $studentId)->first();
    }
}
