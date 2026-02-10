<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Renames unit_cost to price while keeping the data/type intact
            $table->renameColumn('unit_cost', 'price');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            // Revert back if needed
            $table->renameColumn('price', 'unit_cost');
        });
    }
};