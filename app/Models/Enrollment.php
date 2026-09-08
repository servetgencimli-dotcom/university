<?php

namespace App\Models;

use App\Enums\LetterGrade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_section_id',
        'midterm_score',
        'final_score',
        'attendance_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'midterm_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'total_score' => 'decimal:2',
            'attendance_percentage' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function attendanceRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function recalculateAttendancePercentage(): void
    {
        $totalSessions = $this->attendanceRecords()->count();
        if ($totalSessions === 0) {
            return;
        }

        $presentSessions = $this->attendanceRecords()->whereIn('status', ['present', 'excused'])->count();
        $lateSessions = $this->attendanceRecords()->where('status', 'late')->count();

        $effectivePresence = $presentSessions + ($lateSessions * 0.5);
        $percentage = round(($effectivePresence / $totalSessions) * 100, 2);

        $this->attendance_percentage = $percentage;
        $this->saveQuietly();
    }

    // Official Khazar University 5-tier (4.0 scale) grade calculator
    public static function calculateGrade(float $total, string $degreeLevel = 'bachelor'): string
    {
        return LetterGrade::fromScore($total, $degreeLevel)->value;
    }
}
