<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'code',
        'degree_level',
        'duration_years',
        'credits_total',
        'tuition_fee_azn',
        'language',
        'study_mode',
        'overview',
        'career_opportunities',
        'admission_requirements',
        'min_score_required',
        'is_popular',
    ];

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'tuition_fee_azn' => 'decimal:2',
            'duration_years' => 'integer',
            'credits_total' => 'integer',
            'min_score_required' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdmissionsApplication::class);
    }
}
