<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_app', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')
                  ->constrained('app')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_app');
    }
}
