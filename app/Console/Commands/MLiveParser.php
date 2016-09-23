<?php

namespace App\Console\Commands;

use App\Models\ActiveSeason;
use App\Models\ActiveSite;
use App\Services\PlayerListService;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;

class MLiveParser extends ArticleImporter
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsers:articles:mlive
        {--domain= : The domain name of the site to use} 
        {--season= : The season_id to use, defaults to the current}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses articles from the mlive feed';

    /**
     * The url to request
     *
     * @var string
     */
    protected $url = "http://highschoolsports.mlive.com/region/grandrapids/boyswaterpolo/news/?rss";

    /**
     * The key in the settings file for the site
     *
     * @var string
     */
    protected $settingKey = 'mlive';

    /**
     * The name from the site settings to search for
     *
     * @var string
     */
    protected $teamName;

    /**
     * Basic article content before loading and parsing everything
     *
     * @var Collection
     */
    protected $latest;

    /**
     * Array of article id's which were imported
     *
     * @var array
     */
    protected $importedArticles = [];

    /**
     * Count of tags for all the imported articles
     *
     * @var int
     */
    protected $importedTags = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ActiveSite $site, ActiveSeason $season, PlayerListService $playerListService)
    {
        parent::__construct($site, $season, $playerListService);

        $this->teamName = $this->site->settings->get($this->settingKey.'.name');
    }

    /**
     * Gets the last ran timestamp and does all the hard work
     *
     * @param int $lastRan
     * @return array [[article ids], # of tags]
     */
    protected function parse($lastRan)
    {
        $this->latest = $this->getLatest($lastRan);
        $this->logDebug('Found the following articles to import', ['latest' => $this->latest]);

        if (!count($this->latest)) {
            return [[], 0];
        }

        $this->loadArticles($this->latest);

        return [$this->importedArticles, $this->importedTags];
    }

    /**
     * Get's the xml data for all of the articles published since we ran last
     *
     * @param $lastRan int The timestamp of our last run
     * @return \Illuminate\Support\Collection
     */
    protected function getLatest($lastRan)
    {
        $latest = [];
        $feed = new \SimpleXMLElement($this->url, null, true);

        foreach ($feed->channel->item as $item) {
            if ($lastRan > strtotime((string)$item->pubDate)) {
                continue;
            } else {
                $ts = date('Y-m-d g:i:a', strtotime((string)$item->pubDate));
                $latest[] = [
                    'title' => (string)$item->title,
                    'url' => (string)$item->link,
                    // convert the encoded html into html and then get rid of them
                    'description' => trim(strip_tags(html_entity_decode((string)$item->description))),
                    'published' => $ts,
                    'created_at' => $ts,
                    'updated_at' => $ts
                ];
            }
        }

        return collect($latest);
    }

    /**
     * Get's the full content of all the articles in $latest
     *
     * @param Collection $latest Array of article info, each
     * @return mixed
     * @throws
     * @throws \Exception|\Throwable
     */
    protected function loadArticles($latest)
    {
        // $client = $this->guzzleClient();
        $client = new Client();

        $requests = function ($articles) {
            foreach($articles as $data) {
                yield new Request('GET', $data['url']);
            }
        };

        $pool = new Pool($client, $requests($latest), [
            'concurrency' => 5,
            // this is delivered each successful response
            'fulfilled' => [$this, 'requestFulfilled'],
            // this is delivered each failed request
            'rejected' => [$this, 'requestRejected'],
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();
    }

    public function requestFulfilled(Response $response, $index)
    {
        $this->logInfo('fulfilled request', compact('response', 'index'));

        $body = (string) $response->getBody();
        $info = $this->latest[$index];
        $foundPlayers = [];

        // search the article for players
        $crawler = new Crawler($body);
        $content = $crawler->filter('.entry-content')->text();

        $this->playerlist->each(function($playerSeason) use ($foundPlayers, $content) {
            $found_pos = stripos($content, $playerSeason->player->name);
            if($found_pos !== false){
                $foundPlayers[$playerSeason->player->id] = $this->excerptAndHighlight(strip_tags($content), $playerSeason->player->name);
            }
        });

        if (
            count($foundPlayers) > 0
            || strpos($info['title'], $this->teamName) !== false
            || strpos($content, $this->teamName) !== false
        ) {
            try {
                $photo = $crawler->filterXPath('//meta[@property="og:img"]')->attr('content');
                $info['photo'] = $photo;
            } catch (\Exception $e) {
                $info['photo'] = '';
            }

            $this->logInfo('importing', compact('info', 'foundPlayers'));

            $this->articleInsertStmt->execute($info);
            $article_id = $this->pdo->lastInsertId();
            $this->importedArticles[] = $article_id;

            foreach($foundPlayers as $player_id => $highlight){
                $pta = [
                    'article_id' => $article_id,
                    'player_id' => $player_id,
                    'highlight' => $highlight
                ];
                $this->playerToArticleInsertStmt->execute($pta);
                $this->importedTags++;
            }
        } else {
            $this->logInfo('no players or team found', compact('info'));
        }
    }

    public function requestRejected($reason, $index)
    {
        $this->logError('rejected request', compact('reason', 'index'));
    }
}
