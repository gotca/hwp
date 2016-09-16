<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/22/2016
 * Time: 1:55 AM
 */

namespace App\Services\PlayerData\Providers;


use App\Models\Article;
use App\Models\Badge;
use App\Models\Photo;
use App\Models\Player;
use App\Models\PlayerSeason;
use App\Services\PlayerData\Contracts\DataProvider;

class SeasonProvider implements DataProvider
{
    /**
     * @var Player
     */
    protected $player;

    /**
     * @var PlayerSeason
     */
    protected $playerSeason;

    /**
     * SeasonProvider constructor.
     * @param Player $player
     * @param Int $season_id
     */
    public function __construct(Player $player, $season_id)
    {
        $this->player = $player;
        $this->playerSeason = $player->seasons()->where('season_id', $season_id)->firstOrFail();
    }


    /**
     * Get the Player
     * @return \App\Models\Player
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
        return $this->playerSeason->title;
    }

    /**
     * Get the player's number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->playerSeason->number;
    }

    /**
     * Get's the player's team
     *
     * @return string V, JV, or STAFF
     */
    public function getTeam()
    {
        return $this->playerSeason->team;
    }

    /**
     * Get's the player's position
     *
     * @return string FIELD or GOALIE
     */
    public function getPosition()
    {
        return $this->playerSeason->position;
    }

    /**
     * Get's the season id
     *
     * @return Integer
     */
    public function getSeasonId()
    {
        return $this->playerSeason->season_id;
    }

    protected function getPhotoQuery()
    {
        return Photo::allTenants()
            ->select('photos.*')
            ->join('photo_player', 'photos.id', '=', 'photo_player.photo_id')
            ->where('photo_player.season_id', '=', $this->playerSeason->season_id)
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
     * Get the player's badges
     *
     * @return \Illuminate\Support\Collection|\App\Models\Badge[]
     */
    public function getBadges()
    {
        return Badge::select('badges.*')
        ->join('badge_player', 'badges.id', '=', 'badge_player.badge_id')
        ->where('badge_player.season_id', '=', $this->playerSeason->season_id)
        ->where('badge_player.player_id', '=', $this->player->id)
        ->orderBy('badge_player.created_at', 'desc')
        ->get();
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
            ->where('article_player.season_id', '=', $this->playerSeason->season_id)
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
        return $this->playerSeason->statsTotal();
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