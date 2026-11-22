<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medical_knowledge')) {
            return;
        }

        Schema::create('medical_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('general');
            $table->longText('content');
            $table->json('tags')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('embedding')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });

        if (DB::getDriverName() === 'pgsql') {
            try {
                DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
                DB::statement('ALTER TABLE medical_knowledge ALTER COLUMN embedding TYPE vector(1536) USING embedding::vector(1536)');
            } catch (\Throwable $e) {
                // Si l'extension pgvector n'est pas installée sur le serveur, la colonne reste en JSON.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_knowledge');
    }
};
