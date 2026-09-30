<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_hour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->constrained('office_hour_slots')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('booking_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('topic')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->string('cancellation_reason')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->timestamps();

            $table->unique(['slot_id', 'student_id', 'booking_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_hour_bookings');
    }
};
