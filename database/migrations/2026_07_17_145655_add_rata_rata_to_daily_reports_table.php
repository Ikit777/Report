<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->double('rata_spm1')->nullable()->after('soh_ft05');
            $table->double('rata_spm2')->nullable()->after('rata_spm1');
            $table->double('rata_spm3')->nullable()->after('rata_spm2');
            $table->double('rata_ft05')->nullable()->after('rata_spm3');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['rata_spm1', 'rata_spm2', 'rata_spm3', 'rata_ft05']);
        });
    }
};
