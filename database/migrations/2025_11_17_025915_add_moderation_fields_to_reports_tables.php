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
        // Add fields to report_items
        Schema::table('report_items', function (Blueprint $table) {
            $table->enum('decision', ['reject', 'accept'])->nullable()->after('status')->comment('Keputusan admin: reject atau accept');
            $table->enum('action', ['delete', 'suspend', 'warn', 'no-action'])->nullable()->after('decision')->comment('Tindakan yang diambil jika accept');
            $table->text('reject_reason')->nullable()->after('action')->comment('Alasan penolakan jika reject');
            $table->text('action_note')->nullable()->after('reject_reason')->comment('Catatan tambahan untuk tindakan');
        });

        // Add fields to report_users
        Schema::table('report_users', function (Blueprint $table) {
            $table->enum('decision', ['reject', 'accept'])->nullable()->after('status')->comment('Keputusan admin: reject atau accept');
            $table->enum('action', ['delete', 'suspend', 'warn', 'no-action'])->nullable()->after('decision')->comment('Tindakan yang diambil jika accept');
            $table->text('reject_reason')->nullable()->after('action')->comment('Alasan penolakan jika reject');
            $table->text('action_note')->nullable()->after('reject_reason')->comment('Catatan tambahan untuk tindakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_items', function (Blueprint $table) {
            $table->dropColumn(['decision', 'action', 'reject_reason', 'action_note']);
        });

        Schema::table('report_users', function (Blueprint $table) {
            $table->dropColumn(['decision', 'action', 'reject_reason', 'action_note']);
        });
    }
};
