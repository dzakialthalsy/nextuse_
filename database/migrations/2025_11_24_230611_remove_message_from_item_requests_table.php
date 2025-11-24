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
        if (Schema::hasColumn('item_requests', 'message')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropColumn('message');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('item_requests', 'message')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->text('message')->nullable()->after('requested_quantity');
            });
        }
    }
};
