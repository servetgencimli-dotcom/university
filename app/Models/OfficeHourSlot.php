<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeHourSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'max_students_per_slot',
        'location',
        'zoom_link',
        'is_recurring',
        'is_active',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'slot_duration_minutes' => 'integer',
        'max_students_per_slot' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function bookings()
    {
        return $this->hasMany(OfficeHourBooking::class, 'slot_id');
    }
}
