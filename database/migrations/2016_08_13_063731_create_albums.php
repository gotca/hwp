<?php

use App\Database\Schema\Blueprint;
use App\Database\Migrations\Migration;

class CreateAlbums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->site();
            $table->season();
            $table->integer('cover_id')->unsigned();
            $table->string('title');
            $table->timestamps();

            $table->foreign('cover_id')
                ->references('id')->on('photos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->drop('albums');
    }
}
