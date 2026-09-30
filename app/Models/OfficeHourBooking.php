<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeHourBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'student_id',
        'booking_date',
        'start_time',
        'end_time',
        'topic',
        'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function slot()
    {
        return $this->belongsTo(OfficeHourSlot::class, 'slot_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
