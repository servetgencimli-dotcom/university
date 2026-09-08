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
        Schema::create('campus_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Məs: "Əsas Tədris Korpusu A", "Süni İntellekt & Robototexnika Mərkəzi", "Mərkəzi Kitabxana"
            $table->string('code')->unique(); // BLD-A, BLD-AI, LIB-01, DORM-01, SPORT-01
            $table->string('category'); // academic, research_lab, library, dormitory, sports, admin, dining
            $table->text('description')->nullable();
            $table->integer('floors_count')->default(5);
            $table->integer('capacity')->default(500);
            $table->integer('map_x')->default(50); // X coordinate percentage on interactive visual map
            $table->integer('map_y')->default(50); // Y coordinate percentage
            $table->string('image')->nullable();
            $table->json('facilities')->nullable(); // Array of facilities like Wi-Fi, 3D Printer, VR Lab, Cafe
            $table->timestamps();
        });

        Schema::create('research_papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('co_authors')->nullable();
            $table->string('category')->default('Computer Science'); // AI, Quantum Computing, Renewable Energy, Economics
            $table->text('abstract');
            $table->string('journal_name')->nullable(); // IEEE, Nature, Springer, Elsevier
            $table->string('doi')->nullable();
            $table->date('publication_date')->nullable();
            $table->integer('citations_count')->default(0);
            $table->string('download_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('academic'); // academic, tech, cultural, career, sports
            $table->text('description');
            $table->string('speaker_names')->nullable();
            $table->string('venue')->default('Böyük Akt Zalı (Korpus A)');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('capacity')->default(200);
            $table->integer('registered_count')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('ticket_token')->unique();
            $table->string('status')->default('confirmed'); // confirmed, attended, cancelled
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('universitet'); // universitet, elm, beynelxalq, telebe_heyati
            $table->text('summary');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tuition_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('invoice_number')->unique(); // INV-2026-0048
            $table->string('semester_term')->default('2026-Bahar');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('student_theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete(); // Master or PhD student
            $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete(); // Professor
            $table->string('title');
            $table->string('research_field');
            $table->text('abstract')->nullable();
            $table->enum('stage', ['proposal', 'research', 'drafting', 'pre_defense', 'defended'])->default('proposal');
            $table->integer('progress_percentage')->default(25);
            $table->date('expected_defense_date')->nullable();
            $table->decimal('final_grade', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_theses');
        Schema::dropIfExists('tuition_invoices');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('event_rsvps');
        Schema::dropIfExists('events');
        Schema::dropIfExists('research_papers');
        Schema::dropIfExists('campus_buildings');
    }
};
