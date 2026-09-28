<?php

namespace App\Models;

use App\Enums\LetterGrade;
use App\Services\GradingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function attendanceRecords(): HasMany
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

        // Automated 25% Absence Limit Rule (Qayıb Limiti Aşımı):
        // If student missed > 25% of sessions (i.e. presence < 75%) and total recorded sessions >= 4:
        if ($totalSessions >= 4 && $percentage < 75.00) {
            $this->letter_grade = 'F';

            if ($this->student) {
                $courseName = $this->courseSection?->course?->name ?? 'Dərs';
                SystemNotification::send(
                    $this->student,
                    'Qayıb Limit Aşımı Xəbərdarlığı',
                    "Siz '{$courseName}' fənnindən 25%-dən çox qayıb etdiyiniz üçün (Davamiyyət: {$percentage}%) avtomatik F (Kəsildi) aldınız.",
                    'academic_warning',
                    route('portal.attendance-report')
                );
            }
        }

        $this->saveQuietly();
    }

    public function gradeItems(): HasMany
    {
        return $this->hasMany(StudentGradeItem::class);
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(StudentAppeal::class);
    }

    public function recalculateTotalFromComponents(): void
    {
        $items = $this->gradeItems()->with('gradeComponent')->get();
        if ($items->isNotEmpty()) {
            $total = $items->sum('score');
            $degreeLevel = $this->student?->degree_level ?? 'bachelor';
            $letterGrade = LetterGrade::fromScore($total, $degreeLevel);

            $this->total_score = $total;
            $this->letter_grade = $letterGrade->value;
            $this->status = $letterGrade->earnsCredit($degreeLevel) ? 'completed' : 'enrolled';
            $this->saveQuietly();

            if ($this->student) {
                app(GradingService::class)->recalculateStudentGpa($this->student);
            }
        }
    }

    // Official Khazar University 5-tier (4.0 scale) grade calculator
    public static function calculateGrade(float $total, string $degreeLevel = 'bachelor'): string
    {
        return LetterGrade::fromScore($total, $degreeLevel)->value;
    }
}
