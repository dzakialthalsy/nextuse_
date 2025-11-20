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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('judul');
            $table->enum('kategori', [
                'Elektronik',
                'Perabotan',
                'Pakaian',
                'Buku & Alat Tulis',
                'Mainan & Hobi',
                'Olahraga',
                'Dapur',
                'Lainnya'
            ]);
            $table->enum('kondisi', ['baru', 'like-new', 'bekas']);
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->enum('status', ['tersedia', 'reserved', 'habis'])->default('tersedia');
            $table->json('preferensi')->nullable()->comment('Array: giveaway, barter');
            $table->text('catatan_pengambilan')->nullable();
            $table->json('foto_barang')->nullable()->comment('Array of image paths');
            $table->boolean('is_draft')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
