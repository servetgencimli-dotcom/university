<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentThesis extends Model
{
    protected $fillable = [
        'student_id',
        'advisor_id',
        'title',
        'research_field',
        'abstract',
        'stage',
        'progress_percentage',
        'expected_defense_date',
        'final_grade',
    ];

    protected function casts(): array
    {
        return [
            'expected_defense_date' => 'date',
            'progress_percentage' => 'integer',
            'final_grade' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}
