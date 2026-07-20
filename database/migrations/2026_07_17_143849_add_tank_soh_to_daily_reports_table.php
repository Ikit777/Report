<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->double('soh_spm1')->nullable()->after('approved_by');
            $table->double('soh_spm2')->nullable()->after('soh_spm1');
            $table->double('soh_spm3')->nullable()->after('soh_spm2');
            $table->double('soh_ft05')->nullable()->after('soh_spm3');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['soh_spm1', 'soh_spm2', 'soh_spm3', 'soh_ft05']);
        });
    }
};
