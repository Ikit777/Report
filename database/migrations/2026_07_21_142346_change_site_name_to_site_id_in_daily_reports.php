<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Drop old site_name column
            $table->dropColumn('site_name');
            
            // Add site_id foreign key
            $table->foreignId('site_id')->nullable()->after('id')->constrained('sites')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
            
            // Restore site_name
            $table->string('site_name')->nullable()->after('id');
        });
    }
};
