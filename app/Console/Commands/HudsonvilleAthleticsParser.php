<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\HasOGPhoto;
use App\Events\ArticlesImported;
use App\Models\ActiveSeason;
use App\Models\ActiveSite;
use App\Models\Player;
use App\Models\Season;
use App\Models\Site;
use App\Services\PlayerListService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HudsonvilleAthleticsParser extends LoggedCommand
{
    use HasOGPhoto;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsers:articles:hudsonvilleathletics
        {--domain= : The domain name of the site to use} 
        {--season= : The season_id to use, defaults to the current}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses articles from the hudsonville athletics website';

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Season
     */
    protected $season;

    /**
     * @var Collection|Player[]
     */
    protected $playerlist;

    /**
     * @var string
     */
    protected $url = "http://feeds.feedburner.com/hudsonvilleathletics?format=xml";

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \PDOStatement
     */
    protected $articleInsertStmt;

    /**
     * @var \PDOStatement
     */
    protected $playerToArticleInsertStmt;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ActiveSite $site, ActiveSeason $season, PlayerListService $playerListService)
    {
        parent::__construct();

        $this->site = $site;
        $this->season = $season;
        $this->playerlist = $playerListService->all()->flatten();

        $this->preparePdo();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lastRan = $this->site->settings->get('hudsonvilleathletics.lastRun');

        list($imported_articles, $imported_tags) = $this->parse($lastRan);

        $this->site->settings->set('hudsonvilleathletics.lastRun', time());
        if (count($imported_articles) > 0) {
            $event = new ArticlesImported($this->site, $this->season, $imported_articles, $imported_tags);
            event($event);
        }
    }

    protected function preparePdo()
    {
        $this->pdo = DB::connection()->getPdo();

        $this->articleInsertStmt = $this->pdo->prepare("
          INSERT INTO 
            articles 
          SET 
            site_id = ".$this->site->id.",
            season_id = ".$this->season->id.", 
            title = :title, 
            url = :url, 
            photo = :photo,
            description = :description, 
            published = :published,
            created_at = :created_at,
            updated_at = :updated_at
        ");

        $this->playerToArticleInsertStmt = $this->pdo->prepare("
            INSERT INTO 
              article_player 
            SET
              site_id = ".$this->site->id.",
              player_id = :player_id, 
              season_id = ".$this->season->id.", 
              article_id = :article_id, 
              highlight = :highlight,
              created_at = :created_at,
              updated_at = :updated_at
        ");
    }

    protected function parse($lastRan)
    {
        $feed = new \SimpleXMLElement($this->url, null, true);
        $imported_articles = [];
        $imported_tags = 0;
        foreach($feed->channel->item as $item){

            // if the pubdate is less than our last parse just continue on
            if($lastRan > strtotime((string)$item->pubDate)) {
                continue;
            } else {
                // search the article for players
                $found_players = [];
                $content = strip_tags(html_entity_decode((string)$item->children('http://purl.org/rss/1.0/modules/content/')->encoded));
                $this->playerlist->each(function($playerSeason) use ($found_players, $content) {
                    $found_pos = stripos($content, $playerSeason->player->name);
                    if($found_pos !== false){
                        $found_players[$playerSeason->player->id] = $this->excerptAndHighlight(strip_tags($content), $playerSeason->player->name);
                    }
                });

                if(count($found_players)>0){
                    $published = strtotime((string)$item->pubDate);
                    $article = [
                        'title' => (string)$item->title,
                        'url' => (string)$item->link,
                        'photo' => $this->getPhoto((string)$item->link),
                        'description' => trim(strip_tags(html_entity_decode((string)$item->description))),
                        'published' => date('Y-m-d G:i:s', $published),
                        'created_at' => date('Y-m-d G:i:s', $published),
                        'updated_at' => date('Y-m-d G:i:s', $published),
                    ];
                    $this->logDebug('importing', compact('article', 'found_players'));

                    $this->articleInsertStmt->execute($article);
                    $article_id = $this->pdo->lastInsertId();
                    $imported_articles[] = $article_id;

                    foreach($found_players as $player_id => $highlight){
                        $pta = [
                            'article_id' => $article_id,
                            'player_id' => $player_id,
                            'highlight' => $highlight
                        ];
                        $this->playerToArticleInsertStmt->execute($pta);
                        $imported_tags++;
                    }

                } else {
                    continue;
                }
            }
        }

        return [$imported_articles, $imported_tags];
    }


    // 'blah blah blah blah blah blah blah' becomes 'blah blah...'
    protected function excerptAndHighlight($text, $word = NULL, $radius = 50, $highlight_begin = '<strong>', $highlight_end = '</strong>')
    {
        if (!$word) {
            if (strlen($text) > $radius * 2)
                return $this->restoreTags(substr($text, 0, strpos($text, ' ', $radius * 2)) . "...");
            else
                return $text;
        } else {
            $word = trim($word);
            $word_pos = stripos($text, $word);
            if ($word_pos !== false) {
                if ($word_pos - $radius <= 0)
                    $begin_pos = 0;
                else
                    $begin_pos = strpos($text, ' ', max(0, $word_pos - $radius)) + 1;
                $after_pos = strpos($text, ' ', min(strlen($text), $word_pos + strlen($word) + $radius))
                or $after_pos = strlen($text);

                $excerpt = '';
                if ($begin_pos > 0) $excerpt .= '...';
                $excerpt .= substr($text, $begin_pos, $word_pos - $begin_pos);
                $excerpt .= $highlight_begin . substr($text, $word_pos, strlen($word)) . $highlight_end;
                $excerpt .= substr($text, $word_pos + strlen($word), $after_pos - ($word_pos + strlen($word)));
                if ($after_pos < strlen($text)) $excerpt .= '...';

                return $this->restoreTags($excerpt);
            } else {
                return $text;
            }
        }
    }

    //===================================================================================//
    // Original PHP code by Chirp Internet: www.chirp.com.au // Please acknowledge use of this code by including this header.
    protected function restoreTags($input)
    {
        // addition 7-20 AD
        // if input doesn't start with a p tag, add it
        if (strpos($input, '<p>') !== 0)
            $input = '<p>' . $input;

        $opened = $closed = []; // tally opened and closed tags in order

        if (preg_match_all("/<(\/?[a-z]+)>/i", $input, $matches)) {
            foreach ($matches[1] as $tag) {
                if (preg_match("/^[a-z]+$/i", $tag, $regs)) {
                    $opened[] = $regs[0];
                } elseif (preg_match("/^\/([a-z]+)$/i", $tag, $regs)) {
                    $closed[] = $regs[1];
                }
            }
        }
        // use closing tags to cancel out opened tags
        if ($closed) {
            foreach ($opened as $idx => $tag) {
                foreach ($closed as $idx2 => $tag2) {
                    if ($tag2 == $tag) {
                        unset($opened[$idx]);
                        unset($closed[$idx2]);
                        break;
                    }
                }
            }
        }
        // close tags that are still open
        if ($opened) {
            $tagstoclose = array_reverse($opened);
            foreach ($tagstoclose as $tag)
                $input .= "</$tag>";
        }

        return $input;
    }
}
