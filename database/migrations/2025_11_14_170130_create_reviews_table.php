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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewed_organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('organizations')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5 stars
            $table->string('title')->nullable(); // Judul singkat (opsional)
            $table->text('review_text'); // Ulasan (required, 20-500 karakter)
            $table->json('images')->nullable(); // Maksimal 3 gambar
            $table->boolean('show_name')->default(true); // Tampilkan nama reviewer atau anonim
            $table->unsignedBigInteger('transaction_id')->nullable(); // Optional, untuk future use
            $table->timestamps();
            
            // Indexes untuk performa query
            $table->index('reviewed_organization_id');
            $table->index('reviewer_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
