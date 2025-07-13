<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'student_id',
        'score',
        'submitted_at',
        'is_late',
        'resubmission_count',
        'resubmission_date',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
        'resubmission_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function hasResubmissions()
    {
        return $this->resubmission_count > 0;
    }
}
