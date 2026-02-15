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
        Schema::create('event_reschedule_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->dateTime('suggested_start_at');
            $table->dateTime('suggested_end_at');
            $table->text('reason')->nullable();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, declined
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_reschedule_suggestions');
    }
};
