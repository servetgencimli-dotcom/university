<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeComponent extends Model
{
    protected $fillable = [
        'course_section_id',
        'name',
        'max_score',
        'type',
        'order_index',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'order_index' => 'integer',
        ];
    }

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function studentGradeItems(): HasMany
    {
        return $this->hasMany(StudentGradeItem::class);
    }
}
