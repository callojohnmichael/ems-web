<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_finance_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->decimal('logistics_total', 12, 2)->default(0);
            $table->decimal('equipment_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->string('status')->default('pending'); 
            // pending | approved | rejected

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_finance_requests');
    }
};
