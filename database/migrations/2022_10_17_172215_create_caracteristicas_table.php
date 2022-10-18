<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaracteristicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caracteristicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personaje_id');
            $table->integer('nivel');
            $table->integer('vitalidad');
            $table->integer('aguante');
            $table->integer('vigor');
            $table->integer('fuerza');
            $table->integer('destreza');
            $table->integer('aprendizaje');
            $table->integer('inteligencia');
            $table->integer('fe');
            $table->timestamps();


            $table->foreign('personaje_id')->references('id')->on('personajes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caracteristicas');
    }
}
