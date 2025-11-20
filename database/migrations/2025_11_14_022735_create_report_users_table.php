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
        Schema::create('report_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->nullable()->constrained('organizations')->nullOnDelete()->comment('ID organisasi yang melaporkan');
            $table->string('target_user')->comment('Username atau ID pengguna yang dilaporkan');
            $table->string('target_user_name')->nullable()->comment('Nama pengguna yang dilaporkan');
            $table->enum('kategori', [
                'penipuan',
                'spam',
                'pelecehan',
                'akun-palsu',
                'lainnya'
            ]);
            $table->text('deskripsi');
            $table->json('bukti_paths')->nullable()->comment('Array path file bukti');
            $table->enum('status', [
                'pending',
                'dalam-peninjauan',
                'diterima',
                'ditolak',
                'selesai'
            ])->default('pending');
            $table->text('admin_notes')->nullable()->comment('Catatan dari admin');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('organizations')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_users');
    }
};
