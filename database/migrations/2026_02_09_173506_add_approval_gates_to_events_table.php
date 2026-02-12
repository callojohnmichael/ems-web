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
        // These are the missing columns causing the SQL error
        $table->boolean('is_venue_approved')->default(false)->after('status');
        $table->boolean('is_logistics_approved')->default(false)->after('is_venue_approved');
        $table->boolean('is_finance_approved')->default(false)->after('is_logistics_approved');
        
        // Ensure venue_id exists as well if you haven't added it yet
        if (!Schema::hasColumn('events', 'venue_id')) {
            $table->foreignId('venue_id')->nullable()->constrained()->onDelete('set null');
        }
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        $table->dropColumn(['is_venue_approved', 'is_logistics_approved', 'is_finance_approved']);
    });
}


};
