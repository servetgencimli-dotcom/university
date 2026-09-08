<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'semester_term',
        'room_number',
        'day_of_week',
        'start_time',
        'end_time',
        'capacity',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class)->where('is_published', true);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(CourseAnnouncement::class)->latest('published_at');
    }

    public function availableSeatsCount(): int
    {
        $enrolled = $this->enrollments()->whereIn('status', ['enrolled', 'completed'])->count();

        return max(0, $this->capacity - $enrolled);
    }

    public function isFull(): bool
    {
        return $this->availableSeatsCount() <= 0;
    }
}
