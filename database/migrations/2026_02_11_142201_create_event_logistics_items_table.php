<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_logistics_items', function (Blueprint $table) {
            $table->id();

            // Relationship to events table
            $table->foreignId('event_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Manual logistics fields
            $table->string('description'); // e.g. Sound System
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 14, 2);

            $table->timestamps();

            // Optional index for performance
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logistics_items');
    }
};
