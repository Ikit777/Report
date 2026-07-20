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
        Schema::create('daily_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignId('tank_id')->constrained('tanks')->onDelete('cascade');
            
            // Pagi Data
            $table->double('sounding_pagi')->nullable();
            $table->double('liter_pagi')->nullable();
            $table->time('jam_pagi')->nullable();
            $table->string('petugas_pagi')->nullable();
            
            // Sore Data
            $table->double('sounding_sore')->nullable();
            $table->double('liter_sore')->nullable();
            $table->time('jam_sore')->nullable();
            $table->string('petugas_sore')->nullable();
            
            // Angka FM Kecil
            $table->double('fm_pagi')->nullable();
            $table->double('fm_sore')->nullable();
            $table->double('fm_pakai')->nullable();
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_items');
    }
};
