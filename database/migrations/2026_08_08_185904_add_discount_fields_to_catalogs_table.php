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
        Schema::table('catalogs', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('composition');
            $table->string('currency', 3)->default('AED')->after('price');
            $table->unsignedTinyInteger('discount_percent')->nullable()->after('currency')->index();
            $table->timestamp('discount_starts_at')->nullable()->after('discount_percent');
            $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency', 'discount_percent', 'discount_starts_at', 'discount_ends_at']);
        });
    }
};
