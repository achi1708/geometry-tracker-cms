<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookEmpresasAdcampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_empresas_adcampaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adaccount_id')
                  ->constrained('facebook_empresas_adaccounts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('campaign_id');
            $table->string('campaign_name');
            $table->string('objective')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('budget_remaining')->nullable()->default('');
            $table->string('buying_type')->nullable()->default('');
            $table->string('configured_status')->nullable()->default('');
            $table->string('daily_budget')->nullable()->default('');
            $table->string('effective_status')->nullable()->default('');
            $table->string('issues_info')->nullable()->default('');
            $table->string('created_time')->nullable()->default('');
            $table->string('start_time')->nullable()->default('');
            $table->string('stop_time')->nullable()->default('');
            $table->timestamps();
            $table->unique(array('adaccount_id', 'campaign_id'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facebook_empresas_adcampaigns');
    }
}
