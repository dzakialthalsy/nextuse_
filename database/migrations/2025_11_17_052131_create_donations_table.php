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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete()->comment('ID organisasi yang berdonasi (nullable untuk donasi anonim)');
            $table->text('pesan')->nullable()->comment('Pesan dukungan dari donatur');
            $table->boolean('is_anonim')->default(false)->comment('Apakah donasi ini anonim');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->comment('Status verifikasi donasi');
            $table->decimal('jumlah', 15, 2)->nullable()->comment('Jumlah donasi (jika tersedia dari payment gateway)');
            $table->string('payment_reference')->nullable()->comment('Referensi pembayaran dari QRIS');
            $table->timestamp('verified_at')->nullable()->comment('Waktu verifikasi donasi');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
