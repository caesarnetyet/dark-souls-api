<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipos', function (Blueprint $table) {

            $table->unsignedBigInteger('personaje_id');
            $table->unsignedBigInteger('arma_id');
            $table->foreign('personaje_id')->references('id')->on('personajes')->onDelete('cascade');
            $table->foreign('arma_id')->references('id')->on('armas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipos');
    }
}
