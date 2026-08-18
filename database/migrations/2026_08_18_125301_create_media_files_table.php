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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('type', 20);
            $table->string('file_name');
            $table->string('thumbnail_path');
            $table->string('large_path');
            $table->string('original_path');
            $table->string('title', 255);
            $table->string('caption', 255);
            $table->string('status', 20)->default('pending');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
