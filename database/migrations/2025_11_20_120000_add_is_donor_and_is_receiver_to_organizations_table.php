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
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (! Schema::hasColumn('organizations', 'is_donor')) {
                    $table->boolean('is_donor')->default(false)->after('is_admin');
                }
                if (! Schema::hasColumn('organizations', 'is_receiver')) {
                    $table->boolean('is_receiver')->default(true)->after('is_donor');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (Schema::hasColumn('organizations', 'is_receiver')) {
                    $table->dropColumn('is_receiver');
                }
                if (Schema::hasColumn('organizations', 'is_donor')) {
                    $table->dropColumn('is_donor');
                }
            });
        }
    }
};