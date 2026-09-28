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
        // 1. Grade Distribution Components (Müəllimin Fənn üzrə Bal Bölgüsü)
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->string('name'); // e.g. "Davamiyyət", "Seminar & Məşğələ", "Sərbəst İşlər", "Kollokvium / Quiz", "Laboratoriya", "Final İmtahanı"
            $table->decimal('max_score', 5, 2)->default(10.00); // e.g. 10.00, 20.00, 50.00
            $table->string('type')->default('custom'); // attendance, seminar, quiz, assignment, midterm, final, custom
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 2. Student Grade per Component (Tələbənin Hər Komponent üzrə Topladığı Bal)
        Schema::create('student_grade_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('grade_component_id')->constrained('grade_components')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0.00);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'grade_component_id']);
        });

        // 3. Student Appeals & Petitions (Şəkildəki Müraciətlər: İmtahan etirazı, Tamamlama, Cərimə, Dekanlıq)
        Schema::create('student_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->enum('type', [
                'makeup_exam',       // Tamamlama imtahanına müraciət
                'grade_objection',   // İmtahan nəticəsinə etiraz et (Apellyasiya)
                'penalty_exam',      // Cərimə imtahanına müraciət
                'dean_petition',     // Dekanlığa müraciət
            ]);
            $table->string('subject'); // Başlıq və ya Müraciət mövzusu
            $table->text('reason'); // Ətraflı əsaslandırma
            $table->string('attachment_path')->nullable(); // Əlavə sənəd (əgər varsa)
            $table->enum('status', ['pending', 'in_review', 'department_review', 'commission_review', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reviewer_notes')->nullable();
            $table->dateTime('submitted_at');
            $table->dateTime('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_appeals');
        Schema::dropIfExists('student_grade_items');
        Schema::dropIfExists('grade_components');
    }
};
