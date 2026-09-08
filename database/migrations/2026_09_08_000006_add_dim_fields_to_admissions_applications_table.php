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
        Schema::table('admissions_applications', function (Blueprint $table) {
            $table->string('dim_work_number')->nullable()->after('application_number'); // DİM İş Nömrəsi
            $table->integer('secim_sira')->nullable()->default(1)->after('exam_type'); // İxtisas seçim sırası
            $table->string('odenis_formasi')->default('Dövlət Sifarişli')->after('secim_sira'); // Dövlət Sifarişli və ya Ödənişli
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions_applications', function (Blueprint $table) {
            $table->dropColumn(['dim_work_number', 'secim_sira', 'odenis_formasi']);
        });
    }
};
