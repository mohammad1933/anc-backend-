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
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('sku')->unique();
            $table->string('type')->default('plain')->index();
            $table->string('hex_code', 9)->nullable();
            $table->string('color_family')->nullable()->index();
            $table->decimal('price', 10, 2)->nullable();
            $table->char('currency', 3)->default('AED');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('stock_status')->default('in_stock')->index();
            $table->string('swatch_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->unique(['catalog_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
