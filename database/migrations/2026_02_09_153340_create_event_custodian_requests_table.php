<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_custodian_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custodian_material_id')->constrained()->cascadeOnDelete();

            $table->integer('quantity')->default(1);

            // optional: custodian approval status
            $table->string('status')->default('pending'); 
            // pending | approved | rejected | returned

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_custodian_requests');
    }
};
