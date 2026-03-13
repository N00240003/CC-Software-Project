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
        Schema::create('game_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slot_name'); // e.g. "Slot 1", "Slot 2", "Slot 3"
            $table->string('chapter'); // Current Tweego passage name
            $table->json('game_variables'); // To store all Tweego SugarCube variables
            $table->string('c_resolver_type')->nullable(); // What type of "conflict resolver" user is
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_saves');
    }
};
