<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/13/2016
 * Time: 1:34 AM
 */

namespace App\Database\Schema;


class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    public function site() {
        $this->integer('site_id')->unsigned();

        $this->foreign('site_id')
            ->references('id')->on('sites')
            ->onDelete('cascade')
            ->onUpdate('cascade');
    }

    public function season()
    {
        $this->integer('season_id')->unsigned();

        $this->foreign('season_id')
            ->references('id')->on('seasons')
            ->onDelete('cascade')
            ->onUpdate('cascade');
    }

    public function player()
    {
        $this->integer('player_id')->unsigned();
        $this->foreign('player_id')
            ->references('id')->on('players')
            ->onDelete('cascade')
            ->onUpdate('cascade');
    }

    /**
     * Create a new set column on the table.
     *
     * @param  string  $column
     * @param  array   $allowed
     * @return \Illuminate\Support\Fluent
     */
    public function set($column, array $allowed)
    {
        return $this->addColumn('set', $column, compact('allowed'));
    }
}