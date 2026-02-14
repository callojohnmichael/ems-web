<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            $table->foreignId('event_post_id')
                ->after('id')
                ->nullable() // use nullable if table already has rows
                ->constrained('event_posts')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_post_id');
        });
    }
};
