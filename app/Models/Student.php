<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'middle_name',
        'birth_date',
        'gender',
        'contact_number',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function classSections()
    {
        return $this->belongsToMany(ClassSection::class, 'class_section_student')
                    ->withPivot('enrollment_date', 'status')
                    ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameWithIdAttribute()
    {
        return $this->student_id . ' - ' . $this->full_name;
    }
}
