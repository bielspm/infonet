<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servico_prestador', function (Blueprint $table) {
            $table->unsignedBigInteger('prestador_id');
            $table->unsignedBigInteger('servico_id');
            $table->decimal('km_saida', 8, 2);
            $table->decimal('valor_saida', 10, 2);
            $table->decimal('valor_km_excedente', 10, 2);
            $table->timestamps();
            $table->primary(['prestador_id', 'servico_id']);
            $table->foreign('prestador_id')->references('id')->on('prestador')->onDelete('cascade');
            $table->foreign('servico_id')->references('id')->on('servico')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servico_prestador');
    }
};
