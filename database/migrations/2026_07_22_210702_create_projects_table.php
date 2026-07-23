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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('client');
            $table->text('cover_image')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_favorite')->default(false)->index();
            $table->json('fabrics')->nullable();
            $table->json('saved_colors')->nullable();
            $table->json('notes')->nullable();
            $table->json('inspiration_images')->nullable();
            $table->json('members')->nullable();
            $table->json('timeline')->nullable();
            $table->json('recent_activity')->nullable();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();
            $table->index(['status', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
