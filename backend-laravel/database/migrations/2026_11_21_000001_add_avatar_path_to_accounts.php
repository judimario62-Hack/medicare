<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'patients', 'doctors'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (!Schema::hasColumn($tableName, 'avatar_path')) {
                    $table->string('avatar_path')->nullable()->after(
                        $tableName === 'users' ? 'email' : 'id'
                    );
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['users', 'patients', 'doctors'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'avatar_path')) {
                    $table->dropColumn('avatar_path');
                }
            });
        }
    }
};
