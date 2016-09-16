<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/22/2016
 * Time: 1:56 AM
 */

namespace App\Services\PlayerData\Providers;


use App\Models\Article;
use App\Models\Photo;
use App\Models\Player;
use App\Models\PlayerSeason;
use App\Services\PlayerData\Contracts\DataProvider;

class CareerProvider implements DataProvider
{

    /**
     * @var Player
     */
    protected $player;

    /**
     * @var PlayerSeason
     */
    protected $latestSeason;

    /**
     * CareerProvider constructor.
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->latestSeason = $player->seasons->last();
    }

    /**
     * Get the Player
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get the player's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->latestSeason->title;
    }

    /**
     * Get the player's number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->latestSeason->number;
    }

    /**
     * Get's the player's team
     *
     * @return string V, JV, or STAFF
     */
    public function getTeam()
    {
        return $this->latestSeason->team;
    }

    /**
     * Get's the player's position
     *
     * @return string FIELD or GOALIE
     */
    public function getPosition()
    {
        return $this->latestSeason->position;
    }

    /**
     * Get's the season id
     *
     * @return Integer
     */
    public function getSeasonId()
    {
        return 0;
    }

    /**
     * Get's the query for getting the photos
     *
     * @return mixed
     */
    protected function getPhotoQuery()
    {
        return Photo::allTenants()
            ->select('photos.*')
            ->join('photo_player', 'photos.id', '=', 'photo_player.photo_id')
            ->where('photo_player.player_id', '=', $this->player->id)
            ->orderBy('photos.created_at', 'desc');
    }

    /**
     * Get ALL of the player's photos without any pagination
     *
     * @return mixed
     */
    public function getAllPhotos()
    {
        return $this->getPhotoQuery()
            ->get();
    }


    /**
     * Get the player's photos
     *
     * @return \Illuminate\Contracts\Pagination\Paginator|\App\Models\Photo[]
     */
    public function getPhotos()
    {
        return $this->getPhotoQuery()
            ->paginate(48);
    }

    /**
     * Get the player's badges.
     * NOTE - this takes advantage of the fact that badges aren't tenated to the season
     *
     * @return \Illuminate\Support\Collection|\App\Models\Badge[]
     */
    public function getBadges()
    {
        return $this->player->badges;
    }

    /**
     * Get's the player's articles
     *
     * @return \Illuminate\Support\Collection|\App\Models\Article[]
     */
    public function getArticles()
    {
        return Article::allTenants()
            ->select(['articles.*', 'article_player.highlight'])
            ->join('article_player', 'articles.id', '=', 'article_player.article_id')
            ->where('article_player.player_id', '=', $this->player->id)
            ->orderBy('published', 'desc')
            ->get();
    }

    /**
     * Get's the player's stats
     *
     * @return \App\Models\Stat
     */
    public function getStats()
    {
        return $this->player->statsTotal();
    }


    /**
     * Get's all of the player's seasons
     *
     * @return \Illuminate\Support\Collection|\App\Models\PlayerSeason[]
     */
    public function getSeasons()
    {
        return $this->player->seasons;
    }
}