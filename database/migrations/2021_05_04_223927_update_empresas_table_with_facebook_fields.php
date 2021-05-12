<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmpresasTableWithFacebookFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('fb_access_token');
            $table->string('fb_token_time');
            $table->string('fb_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropIfExist('fb_access_token');
        $table->dropIfExist('fb_token_time');
        $table->dropIfExist('fb_account_id');
    }
}
