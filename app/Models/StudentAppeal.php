<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAppeal extends Model
{
    protected $fillable = [
        'student_id',
        'course_section_id',
        'enrollment_id',
        'type',
        'subject',
        'reason',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewer_notes',
        'submitted_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'makeup_exam' => 'Tamamlama imtahanına müraciət',
            'grade_objection' => 'İmtahan nəticəsinə etiraz (Apellyasiya)',
            'penalty_exam' => 'Cərimə imtahanına müraciət',
            'dean_petition' => 'Dekanlığa müraciət',
            default => 'Müraciət',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'in_review' => 'badge-primary',
            'department_review' => 'badge-info',
            'commission_review' => 'badge-master',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-primary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Gözləmədə (İlkin)',
            'in_review' => 'Fənn Müəllimindədir',
            'department_review' => 'Kafedra Sədrindədir',
            'commission_review' => 'Apellyasiya Komissiyasındadır',
            'approved' => 'Təsdiq Edildi',
            'rejected' => 'İmtina Edildi',
            default => 'Naməlum',
        };
    }
}
