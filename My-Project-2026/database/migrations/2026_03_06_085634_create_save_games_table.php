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
        Schema::create('save_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('slot'); // e.g. "Slot 1", "Slot 2", "Slot 3"
            $table->string('chapter'); // Current Tweego passage name
            $table->json('game_variables'); // To store all Tweego SugarCube variables
            $table->string('resolver_type')->nullable(); // What type of "conflict resolver" user is
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('save_games');
    }
};
