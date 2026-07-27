<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_report_items', function (Blueprint $table) {
            $table->string('main_hole_variant')->nullable()->after('tank_id')
                ->comment('For tanks with (DEPAN + BELAKANG) / 2: stores DEPAN, BELAKANG, or (DEPAN + BELAKANG) / 2');
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_items', function (Blueprint $table) {
            $table->dropColumn('main_hole_variant');
        });
    }
};
