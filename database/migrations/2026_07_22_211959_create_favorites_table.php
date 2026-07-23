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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('favorite_folder_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('catalog_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('collection');
            $table->string('material');
            $table->text('image_url');
            $table->json('colors')->nullable();
            $table->boolean('is_in_quote')->default(false)->index();
            $table->timestamps();
            $table->index(['favorite_folder_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
