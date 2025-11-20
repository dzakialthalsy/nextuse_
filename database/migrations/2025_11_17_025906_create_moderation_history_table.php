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
        Schema::create('moderation_history', function (Blueprint $table) {
            $table->id();
            $table->string('reportable_type')->comment('Model class: ReportItem atau ReportUser');
            $table->unsignedBigInteger('reportable_id')->comment('ID dari report item atau report user');
            $table->foreignId('moderator_id')->nullable()->constrained('organizations')->nullOnDelete()->comment('ID admin yang melakukan moderasi');
            $table->string('action')->comment('Tindakan yang dilakukan');
            $table->text('reason')->nullable()->comment('Alasan tindakan');
            $table->enum('status', ['pending', 'approved', 'rejected', 'escalated'])->default('pending');
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_history');
    }
};
