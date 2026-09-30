<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceQrSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_section_id',
        'teacher_id',
        'session_code',
        'session_date',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'session_date' => 'date',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function courseSection()
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
