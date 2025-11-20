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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->uuid('conversation_id')->nullable()->after('id')->index();
            $table->foreignId('item_id')->nullable()->after('conversation_id')->constrained('items')->nullOnDelete();
            $table->foreignId('seller_id')->nullable()->after('item_id')->constrained('organizations')->nullOnDelete();
            $table->foreignId('buyer_id')->nullable()->after('seller_id')->constrained('organizations')->nullOnDelete();
            $table->string('seller_name')->nullable()->after('buyer_id');
            $table->string('buyer_name')->nullable()->after('seller_name');
            $table->string('item_title')->nullable()->after('buyer_name');
            $table->boolean('is_read')->default(false)->after('is_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn([
                'conversation_id',
                'item_id',
                'seller_id',
                'buyer_id',
                'seller_name',
                'buyer_name',
                'item_title',
                'is_read',
            ]);
        });
    }
};

