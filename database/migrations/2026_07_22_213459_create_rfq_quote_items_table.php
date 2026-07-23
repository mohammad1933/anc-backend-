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
        Schema::create('rfq_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rfq_quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('favorite_id')->nullable()->constrained()->nullOnDelete();
            $table->string('collection_name');
            $table->string('reference')->nullable();
            $table->text('image_url');
            $table->string('selected_color');
            $table->string('color_hex')->nullable();
            $table->string('price_group');
            $table->decimal('estimated_quantity', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_quote_items');
    }
};
