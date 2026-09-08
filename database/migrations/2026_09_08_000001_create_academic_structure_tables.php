<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique(); // e.g. IT, ENG, BUS, MED, LAW
            $table->string('dean_name')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Lucide or Heroicon icon identifier
            $table->string('image')->nullable();
            $table->string('color')->default('blue'); // Tailwind color palette theme (e.g. indigo, emerald, amber, rose, cyan)
            $table->integer('established_year')->default(2010);
            $table->integer('students_count')->default(0);
            $table->integer('professors_count')->default(0);
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique(); // e.g. CS, AI, CYBER
            $table->string('head_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique(); // e.g. CS-BSC, AI-MSC, MBA-EXEC
            $table->enum('degree_level', ['bachelor', 'master', 'phd'])->default('bachelor');
            $table->integer('duration_years')->default(4);
            $table->integer('credits_total')->default(240);
            $table->decimal('tuition_fee_azn', 10, 2)->default(3500.00);
            $table->string('language')->default('az'); // az, en, ru, az/en
            $table->string('study_mode')->default('Əyani'); // Əyani (Full-Time), Qiyabi (Part-time), Distant
            $table->text('overview')->nullable();
            $table->text('career_opportunities')->nullable();
            $table->text('admission_requirements')->nullable();
            $table->integer('min_score_required')->default(450); // DİM or SAT minimal threshold
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // e.g. CS101, MATH202
            $table->string('name');
            $table->integer('credits')->default(6);
            $table->integer('semester')->default(1); // 1 to 8
            $table->enum('type', ['compulsory', 'elective'])->default('compulsory'); // Məcburi / Seçmə fənn
            $table->text('syllabus')->nullable();
            $table->timestamps();
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('semester_term')->default('2026-Bahar'); // e.g. 2026-Payız, 2026-Bahar
            $table->string('room_number')->default('Aud. 302');
            $table->string('day_of_week')->default('Bazar ertəsi'); // Bazar ertəsi, Çərşənbə axşamı, etc.
            $table->time('start_time')->default('09:00');
            $table->time('end_time')->default('10:30');
            $table->integer('capacity')->default(30);
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->decimal('midterm_score', 5, 2)->default(0); // 0-50
            $table->decimal('final_score', 5, 2)->default(0); // 0-50
            $table->decimal('total_score', 5, 2)->default(0); // 0-100
            $table->string('letter_grade', 3)->default('F'); // A, B, C, D, E, F
            $table->decimal('attendance_percentage', 5, 2)->default(100.00);
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
