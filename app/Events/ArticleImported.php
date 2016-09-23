<?php

namespace App\Events;

use App\Events\Contracts\Recent as RecentEvent;
use App\Models\Recent as Recent;
use App\Models\Season;
use App\Models\Site;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArticleImported implements RecentEvent, ShouldQueue
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Site
     */
    public $site;

    /**
     * @var Season
     */
    public $season;

    /**
     * @var int
     */
    public $articleId;

    /**
     * Create a new event
     *
     * @param Site $site
     * @param Season $season
     * @param array $articleIds
     *
     * @return void
     */
    public function __construct(Site $site, Season $season, $articleId)
    {
        $this->site = $site;
        $this->season = $season;
        $this->articleId = $articleId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * Get the value for site_id
     *
     * @return integer
     */
    public function getSiteId()
    {
        return $this->site->id;
    }

    /**
     * Get the value for season_id
     *
     * @return integer
     */
    public function getSeasonId()
    {
        return $this->season->id;
    }

    /**
     * Get the value for renderer
     *
     * @return string
     */
    public function getRenderer()
    {
        return Recent::TYPE_ARTICLES;
    }

    /**
     * Get the value for content
     *
     * @return string
     */
    public function getContent()
    {
        return json_encode([$this->articleId]);
    }

    /**
     * Get the value for sticky
     *
     * @return boolean
     */
    public function getSticky()
    {
        return false;
    }
}
