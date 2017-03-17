<?php

use Illuminate\Database\Seeder;

class ImportSeeder extends Seeder
{

    protected $seeds = [
        \Import\Sites::class,
        \Import\Seasons::class,
        \Import\Badges::class,
        \Import\Recent::class,
        \Import\BadgeSeason::class,
        \Import\Locations::class,
        \Import\Photos::class,
        \Import\Albums::class,
        \Import\AlbumPhoto::class,
        \Import\Articles::class,
        \Import\Players::class,
        \Import\PlayerSeason::class,
        \Import\ArticlePlayer::class,
        \Import\BadgePlayer::class,
        \Import\PhotoPlayer::class,
        \Import\Tournaments::class,
        \Import\Games::class,
        \Import\GameUpdateDumps::class,
        \Import\GameStatDumps::class,
        \Import\Stats::class,
        \Import\Advantages::class
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->seeds as $seed) {
            $this->call($seed);
        }
    }
}
