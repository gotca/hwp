<?php

use App\Database\Schema\Blueprint;
use App\Database\Migrations\Migration;

class CreatePhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->site();
            $table->season();
            $table->string('file');
            $table->integer('width');
            $table->integer('height');
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
        $this->schema->drop('photos');
    }
}
