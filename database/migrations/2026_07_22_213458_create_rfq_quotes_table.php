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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_quotes');
    }
};
