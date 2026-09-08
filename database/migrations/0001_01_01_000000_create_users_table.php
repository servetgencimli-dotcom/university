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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('student'); // system_admin, admin, teacher, student, master_student, applicant
            $table->string('role_title')->nullable(); // e.g. "Sistem Administratoru", "Kafedra Müdiri", "Dosent", "Bakalavr (IV Kurs)"
            $table->string('student_id')->nullable()->unique(); // e.g. STU-2026-1042
            $table->string('staff_id')->nullable()->unique(); // e.g. EMP-2024-0018
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('degree_level')->nullable(); // bachelor, master, phd, staff
            $table->decimal('gpa', 3, 2)->default(0.00);
            $table->integer('earned_credits')->default(0);
            $table->string('status')->default('active'); // active, graduated, suspended, pending
            $table->text('bio')->nullable();
            $table->integer('admission_year')->nullable();
            $table->json('permissions')->nullable(); // Custom granular permissions override
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
