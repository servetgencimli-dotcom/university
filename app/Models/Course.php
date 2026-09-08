<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'program_id',
        'prerequisite_course_id',
        'code',
        'name',
        'credits',
        'semester',
        'type',
        'syllabus',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function prerequisite(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }
}
