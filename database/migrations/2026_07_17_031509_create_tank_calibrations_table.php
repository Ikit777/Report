<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tank_calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained()->onDelete('cascade');
            $table->decimal('sounding_cm', 8, 2)->index(); // Sounding (CM)
            $table->integer('sounding_mm')->index(); // Sounding (MM) for fast lookup
            $table->decimal('volume_liters', 15, 2); // Volume (L)
            $table->timestamps();
            $table->unique(['tank_id', 'sounding_mm']); // Unique record per milliliter/millimeter sounding
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tank_calibrations');
    }
};
