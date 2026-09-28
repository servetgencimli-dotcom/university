<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGradeItem extends Model
{
    protected $fillable = [
        'enrollment_id',
        'grade_component_id',
        'score',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function gradeComponent(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class);
    }
}
