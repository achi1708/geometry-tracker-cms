<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstagramMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('media_id')->unique();
            $table->string('ig_id');
            $table->mediumText('caption')->nullable();
            $table->string('comments_count')->nullable();
            $table->string('like_count')->nullable();
            $table->string('media_product_type')->nullable();
            $table->string('media_type')->nullable();
            $table->mediumText('media_url')->nullable();
            $table->json('owner');
            $table->string('permalink')->nullable();
            $table->string('timestamp')->nullable();
            $table->string('username')->nullable();
            $table->json('insights');
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
        Schema::dropIfExists('instagram_media');
    }
}
