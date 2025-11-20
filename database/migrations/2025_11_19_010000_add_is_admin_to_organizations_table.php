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
        if (Schema::hasTable('organizations') && ! Schema::hasColumn('organizations', 'is_admin')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('is_active')->comment('Akun admin platform');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations', 'is_admin')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }
};