<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequesitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requesitos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arma_id');
            $table->integer('fuerza');
            $table->integer('destreza');
            $table->integer('inteligencia');
            $table->integer('fe');
            $table->timestamps();
            $table->foreign('arma_id')->references('id')->on('armas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requesitos');
    }
}
