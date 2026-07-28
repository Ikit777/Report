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
        Schema::create('flowmeters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('unit', 50); // Unit/Nama flowmeter (contoh: FM-001, Unit A)
            $table->string('jenis', 100); // Jenis flowmeter (contoh: Digital, Analog, Turbine)
            $table->string('nomor_seri', 100)->nullable(); // Nomor seri flowmeter
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flowmeters');
    }
};
