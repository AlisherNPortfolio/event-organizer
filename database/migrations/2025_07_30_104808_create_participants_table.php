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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id');
            $table->uuid('user_id');
            $table->boolean('attended')->default(false);
            $table->boolean('marked')->default(false);
            $table->timestamps();

            $table->foreign(columns: 'event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign(columns: 'user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(columns: ['event_id', 'user_id']);
            $table->index(['event_id']);
            $table->index(columns: ['user_id']);
            $table->index(columns: ['attended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex(index: ['event_id']);
            $table->dropForeign(index: ['event_id']);

            $table->dropIndex(index: ['user_id']);
            $table->dropForeign(index: ['user_id']);

            $table->dropIndex(index: ['attended']);

            $table->dropUnique(['event_id', 'user_id']);
        });
        Schema::dropIfExists('participants');
    }
};
