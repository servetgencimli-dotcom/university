<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionsApplication extends Model
{
    protected $fillable = [
        'application_number',
        'user_id',
        'program_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'passport_fin',
        'birth_date',
        'gender',
        'nationality',
        'high_school_gpa',
        'exam_score',
        'exam_type',
        'dim_work_number',
        'secim_sira',
        'odenis_formasi',
        'degree_level',
        'status',
        'reviewer_notes',
        'interview_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'interview_at' => 'datetime',
            'high_school_gpa' => 'decimal:2',
            'exam_score' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
