<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookPublishPostsProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_publish_posts_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                  ->constrained('facebook_publish_posts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('type');
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
        Schema::dropIfExists('facebook_publish_posts_properties');
    }
}
