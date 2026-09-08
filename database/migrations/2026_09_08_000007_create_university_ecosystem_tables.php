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
        // 1. Academic Terms & Prerequisite
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. 2026-Bahar, 2026-Payız
            $table->string('name'); // 2026 Yaz / Bahar Semestri
            $table->date('start_date')->nullable(); // Semester start
            $table->date('end_date')->nullable();   // Semester end
            $table->boolean('is_current')->default(false);
            $table->boolean('registration_open')->default(true);
            $table->date('registration_start_date')->nullable();
            $table->date('registration_end_date')->nullable();
            $table->integer('max_credits_limit')->default(30);
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('prerequisite_course_id')->nullable()->after('program_id')->constrained('courses')->nullOnDelete();
        });

        // 2. Per-Session Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->date('session_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 3. LMS - Assignments & Submissions
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date');
            $table->decimal('max_score', 5, 2)->default(10.00);
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('submission_text')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();
        });

        // 4. LMS - Course Materials & Announcements
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['file', 'link', 'video', 'syllabus'])->default('file');
            $table->text('file_path_or_url');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('course_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->dateTime('published_at');
            $table->timestamps();
        });

        // 5. Finance Enhancements on Invoices
        Schema::table('tuition_invoices', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('receipt_number')->nullable()->after('transaction_id');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('amount');
            $table->json('gateway_response')->nullable()->after('receipt_number');
        });

        // 6. Audit Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. GRADE_UPDATED, ROLE_CHANGED, PASSWORD_RESET, COURSE_ENROLLED, PAYMENT_COMPLETED
            $table->string('subject_type')->nullable(); // Model class
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        // 7. In-App Notifications
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('system'); // grade, assignment, finance, academic, system
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
        Schema::dropIfExists('activity_logs');
        Schema::table('tuition_invoices', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'receipt_number', 'paid_amount', 'gateway_response']);
        });
        Schema::dropIfExists('course_announcements');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('attendance_records');
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_course_id']);
            $table->dropColumn(['prerequisite_course_id']);
        });
        Schema::dropIfExists('academic_terms');
    }
};
