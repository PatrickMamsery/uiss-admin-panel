<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('caption');
            $table->string('url');
            $table->boolean('visibility')->default(1);
            $table->unsignedBigInteger("album_id");
            $table->timestamps();

            $table->index("album_id", "fk_albums_images_idx");

            $table->foreign("album_id", "fk_albums_images_idx")
                ->references("id")->on("albums")
                ->onDelete('cascade')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
