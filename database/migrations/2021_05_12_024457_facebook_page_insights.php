<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookPageInsights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_page_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('metric');
            $table->date('metric_date');
            $table->string('metric_value');
            $table->timestamps();
            $table->unique(array('empresa_id', 'metric', 'metric_date'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_page_insights');
    }
}
