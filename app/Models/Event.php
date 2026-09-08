<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'speaker_names',
        'venue',
        'start_time',
        'end_time',
        'capacity',
        'registered_count',
        'image',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_featured' => 'boolean',
            'capacity' => 'integer',
            'registered_count' => 'integer',
        ];
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }
}
