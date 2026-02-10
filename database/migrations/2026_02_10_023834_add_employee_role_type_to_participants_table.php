<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('event_id');
            }
            if (!Schema::hasColumn('participants', 'role')) {
                $table->string('role')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('participants', 'type')) {
                $table->string('type')->nullable()->after('role');
            }

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['employee_id', 'role', 'type']);
        });
    }
};

