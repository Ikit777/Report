<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            
            // Dari -> Ke
            $table->string('dari_tangki')->nullable();
            $table->string('ke_tangki')->nullable();
            
            // Sonding Tangki SPM
            $table->double('spm_awal')->nullable();
            $table->double('spm_akhir')->nullable();
            $table->double('spm_hasil')->nullable(); // SPM_awal - SPM_akhir
            $table->double('spm_liter')->nullable(); // Auto looked up or calculated
            
            // Sonding Tangki FT
            $table->double('ft_awal')->nullable();
            $table->double('ft_akhir')->nullable();
            $table->double('ft_hasil')->nullable(); // FT_akhir - FT_awal
            $table->double('ft_liter')->nullable(); // Auto looked up or calculated
            
            // Flow Meter
            $table->double('fm_awal')->nullable();
            $table->double('fm_akhir')->nullable();
            $table->double('fm_jumlah')->nullable(); // fm_akhir - fm_awal
            
            // Jam Transfer
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lama_transfer')->nullable(); // calculated duration or manual text
            
            $table->timestamps();
        });

        Schema::create('daily_report_flowmeters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            
            $table->string('unit')->nullable(); // e.g. FSO6003, FT05
            $table->string('jenis_flowmeter')->nullable(); // e.g. TCS
            $table->string('nomor_seri')->nullable();
            $table->double('awal_pagi')->nullable();
            $table->double('akhir_sore')->nullable();
            $table->double('jumlah_pakai')->nullable(); // akhir_sore - awal_pagi
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_flowmeters');
        Schema::dropIfExists('daily_report_transfers');
    }
};
