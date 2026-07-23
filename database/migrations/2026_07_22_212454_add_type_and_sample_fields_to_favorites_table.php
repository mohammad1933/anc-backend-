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
        Schema::table('favorites', function (Blueprint $table) {
            $table->string('type')->default('collection')->index()->after('catalog_id');
            $table->timestamp('sample_requested_at')->nullable()->index()->after('is_in_quote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['sample_requested_at']);
            $table->dropColumn(['type', 'sample_requested_at']);
        });
    }
};
