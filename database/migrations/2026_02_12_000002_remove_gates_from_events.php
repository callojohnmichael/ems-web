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
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'is_venue_approved',
                'is_logistics_approved',
                'is_finance_approved',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_venue_approved')->default(false);
            $table->boolean('is_logistics_approved')->default(false);
            $table->boolean('is_finance_approved')->default(false);
        });
    }
};
