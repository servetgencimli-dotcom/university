<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = [
        'code',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'registration_open',
        'registration_start_date',
        'registration_end_date',
        'max_credits_limit',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'registration_open' => 'boolean',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'max_credits_limit' => 'integer',
        ];
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        return (bool) $this->registration_open;
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first() ?? static::latest()->first();
    }
}
