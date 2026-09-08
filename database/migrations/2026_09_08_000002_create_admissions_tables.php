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
        Schema::create('admissions_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique(); // e.g. APP-2026-8741
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('passport_fin')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->default('Azərbaycan');
            $table->decimal('high_school_gpa', 4, 2)->nullable(); // e.g. 4.80 or 95.00
            $table->integer('exam_score')->nullable(); // DİM və ya SAT balı
            $table->string('exam_type')->default('DİM'); // DİM, SAT, IELTS, Beynəlxalq Olimpiada
            $table->enum('degree_level', ['bachelor', 'master', 'phd'])->default('bachelor');
            $table->enum('status', ['submitted', 'under_review', 'interview_scheduled', 'accepted', 'rejected'])->default('submitted');
            $table->text('reviewer_notes')->nullable();
            $table->dateTime('interview_at')->nullable();
            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admissions_application_id')->constrained()->cascadeOnDelete();
            $table->string('document_type'); // Attestat / Diplom, Transkript, Şəxsiyyət Vəsiqəsi, Motivasiya Məktubu, Foto
            $table->string('file_path');
            $table->string('original_name');
            $table->integer('file_size_kb')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('admissions_applications');
    }
};
