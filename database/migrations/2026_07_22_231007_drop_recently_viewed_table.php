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
        Schema::dropIfExists('recently_viewed');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('image_url');
            $table->text('destination_url')->nullable();
            $table->json('colors')->nullable();
            $table->boolean('is_favorite')->default(false)->index();
            $table->timestamp('quote_requested_at')->nullable()->index();
            $table->timestamp('viewed_at')->nullable()->index();
            $table->timestamps();
            $table->index(['user_id', 'viewed_at']);
        });
    }
};
