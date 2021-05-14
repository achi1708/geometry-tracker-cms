<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookEmpresasAds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_empresas_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adcampaign_id')
                  ->constrained('facebook_empresas_adcampaigns')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('ad_id');
            $table->string('name');
            $table->json('insights');
            $table->timestamps();
            $table->unique(array('adcampaign_id', 'ad_id'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_empresas_ads');
    }
}
