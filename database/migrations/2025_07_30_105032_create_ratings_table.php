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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->integer('value')->default(0);
            $table->string('reason')->nullable();
            $table->uuid('related_event_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('related_event_id')->references('id')->on('events')->onDelete('set null');

            $table->index(['user_id']);
            $table->index(['value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropIndex(index: ['user_id']);
            $table->dropForeign(index: ['user_id']);

            $table->dropIndex(index: ['value']);
            $table->dropForeign(index: ['related_event_id']);
        });
        Schema::dropIfExists('ratings');
    }
};
