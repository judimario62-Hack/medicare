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
    Schema::create('consultations', function (Blueprint $table) {
        $table->id();

        $table->foreignId('appointment_id')
            ->unique()
            ->constrained()
            ->restrictOnDelete();

        $table->text('subjective')->nullable();
        $table->text('objective')->nullable();
        $table->text('assessment')->nullable();
        $table->text('plan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
