<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_hour_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('day_of_week');              // Çərşənbə
            $table->time('start_time');                 // 14:00
            $table->time('end_time');                   // 16:00
            $table->integer('slot_duration_minutes')->default(20);
            $table->integer('max_students_per_slot')->default(3);
            $table->string('location')->nullable();     // 309-cu otaq
            $table->string('zoom_link')->nullable();    // https://zoom.us/j/...
            $table->boolean('is_recurring')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_hour_slots');
    }
};
