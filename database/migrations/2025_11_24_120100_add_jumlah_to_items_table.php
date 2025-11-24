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
        if (Schema::hasTable('items') && ! Schema::hasColumn('items', 'jumlah')) {
            Schema::table('items', function (Blueprint $table) {
                $table->unsignedInteger('jumlah')->default(1)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('items') && Schema::hasColumn('items', 'jumlah')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('jumlah');
            });
        }
    }
};

