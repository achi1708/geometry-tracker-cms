<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookPublishPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_publish_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('post_id')->unique();
            $table->mediumText('message');
            $table->json('application');
            $table->string('is_expired');
            $table->string('is_hidden');
            $table->string('is_popular');
            $table->string('is_published');
            $table->json('message_tags');
            $table->mediumText('picture');
            $table->json('properties');
            $table->json('insights');
            $table->string('created_time');
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
        //
    }
}
