<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeEmpresasNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE empresas MODIFY fb_access_token VARCHAR(255) NULL;');
        DB::statement('ALTER TABLE empresas MODIFY fb_token_time VARCHAR(255) NULL;');
        DB::statement('ALTER TABLE empresas MODIFY fb_account_id VARCHAR(255) NULL;');
        DB::statement('ALTER TABLE empresas MODIFY fb_user_logged_id VARCHAR(255) NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
