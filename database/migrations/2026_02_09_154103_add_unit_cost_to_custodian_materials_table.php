<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('custodian_materials', function (Blueprint $table) {
            $table->decimal('unit_cost', 12, 2)->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('custodian_materials', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });
    }
};
