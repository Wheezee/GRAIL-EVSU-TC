<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecitationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'recitation_id',
        'student_id',
        'score',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function recitation()
    {
        return $this->belongsTo(Recitation::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
