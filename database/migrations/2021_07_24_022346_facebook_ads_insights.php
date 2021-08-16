<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookAdsInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_ads_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')
                  ->constrained('facebook_empresas_ads')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('metric');
            $table->string('metric_value');
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
        Schema::dropIfExists('facebook_ads_insights');
    }
}
