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
        Schema::dropIfExists('rfq_quote_items');
        Schema::dropIfExists('rfq_quotes');
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex(['is_in_quote']);
        });
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn('is_in_quote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->boolean('is_in_quote')->default(false)->index();
        });

        Schema::create('rfq_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_number')->nullable()->unique();
            $table->string('status')->default('draft')->index();
            $table->text('special_requirements')->nullable();
            $table->date('preferred_delivery_date')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_budget')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamp('expected_response_at')->nullable();
            $table->timestamps();
        });

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
};
