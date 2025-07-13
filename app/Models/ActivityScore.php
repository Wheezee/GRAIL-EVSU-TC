<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'student_id',
        'score',
        'is_late',
        'submitted_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_late' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
