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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_request_id')->constrained('report_requests')->cascadeOnDelete();
            $table->string('file_path', 255);
            $table->string('file_name', 150)->nullable();
            $table->string('file_type', 20)->default('pdf');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['report_request_id']);
            $table->index(['generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
