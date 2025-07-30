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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organizer_id');
            $table->string('title', 100);
            $table->text('description');
            $table->string('address');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->integer('min_participants');
            $table->integer('max_participants');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('UZS');
            $table->boolean('is_free')->default(true);
            $table->json('images');
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->timestamps();

            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['organizer_id']);
            $table->index(['status']);
            $table->index(['start_time']);
            $table->index(['is_free']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['organizer_id']);
            $table->dropForeign(['organizer_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['start_time']);
            $table->dropIndex(['is_free']);
        });
        Schema::dropIfExists('events');
    }
};
