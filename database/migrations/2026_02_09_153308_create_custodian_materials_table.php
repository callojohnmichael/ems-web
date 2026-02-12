<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('custodian_materials', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Chair, Table, Speaker
            $table->string('category')->nullable(); // optional (equipment, furniture, sound, etc.)
            $table->integer('stock')->nullable(); // optional (how many available)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custodian_materials');
    }
};
