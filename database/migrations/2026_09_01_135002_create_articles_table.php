<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            // Every editorial field below is intentionally nullable — no
            // field on the article form is mandatory, so a draft can be
            // saved at any stage of completion.
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('introduction')->nullable();
            $table->text('editors_note')->nullable();
            $table->text('authors_note')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
