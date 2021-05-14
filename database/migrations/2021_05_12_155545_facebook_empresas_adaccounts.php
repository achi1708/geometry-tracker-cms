<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookEmpresasAdaccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_empresas_adaccounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('account_id');
            $table->string('account_name');
            $table->tinyInteger('status');
            $table->timestamps();
            $table->unique(array('empresa_id', 'account_id'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_empresas_adaccounts');
    }
}
