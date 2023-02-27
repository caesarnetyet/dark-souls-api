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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->integer('vitality');
            $table->integer('attunement');
            $table->integer('endurance');
            $table->integer('strength');
            $table->integer('dexterity');
            $table->integer('resistance');
            $table->integer('intelligence');
            $table->integer('faith');
            $table->foreignId('classe_id')->constrained('classes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
