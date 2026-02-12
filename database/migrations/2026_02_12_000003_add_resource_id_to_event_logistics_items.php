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
        Schema::table('event_logistics_items', function (Blueprint $table) {
            $table->foreignId('resource_id')->nullable()->constrained('resources')->nullOnDelete()->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_logistics_items', function (Blueprint $table) {
            $table->dropForeignIdFor('resources');
            $table->dropColumn('resource_id');
        });
    }
};
