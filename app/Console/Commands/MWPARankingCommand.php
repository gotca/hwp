<?php

namespace App\Console\Commands;

use App\Models\ActiveSeason;
use App\Models\ActiveSite;
use App\Models\Rank;
use App\Models\Ranking;
use App\Models\Season;
use App\Models\Site;
use App\Notifications\RankingsUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Goutte\Client as GoutteClient;
use Symfony\Component\DomCrawler\Crawler;

class MWPARankingCommand extends LoggedCommand
{
    /**
     * How long to cache the parsed rankings, in minutes
     */
    const CACHE_FOR = 50;

    /**
     * The prefix for the key in the cache
     */
    const CACHE_KEY_PREFIX = 'mwpa.rankings.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsers:mwpa:rankings 
        {--domain= : The domain name of the site to use} 
        {--season= : The season_id to use, defaults to the current}
        {--W|week= : The week to look for, defaults to the site settings}
        {--G|gender= : The gender to use (B|G), defaults to the site settings}
        {--N|name= : The name to look for, defaults to the site settings}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses weekly rankings for teams from the MWPA website';

    /**
     * The base url to query
     *
     * @var string
     */
    protected $urlBase = 'http://michiganwaterpolo.com/both/rankings/index.php';

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Season
     */
    protected $season;

    /**
     * Create a new command instance.
     * TenantServiceProvider handles setting up the site and season injection based on the CLI options
     *
     * @return void
     */
    public function __construct(ActiveSite $site, ActiveSeason $season)
    {
        parent::__construct();

        $this->site = $site;
        $this->season = $season;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $defaults = (array)$this->site->settings->get('ranking.parameters');
        $options = array_merge($defaults, array_filter($this->option()));
        $query = array_intersect_key($options, ['gender'=>null, 'week'=>null]);

        $rankings = $this->getRankings($query);

        // if there's no rankings yet, stop processing
        if ($rankings === false) {
            return;
        }

        $findSelf = function($item) use ($options) {
            return $item->team === $options['name'];
        };

        // look for the current team
        $newRank = $rankings->ranks->first($findSelf);
        if ($newRank) {
            $newRank->self = true;
        }

        $lastRank = Ranking::latest()->first()
            ->ranks
            ->first($findSelf);

        $rankings->save();
        $rankings->ranks()->saveMany($rankings->ranks);

        // update the season ranking data
        $this->season->ranking = $newRank ? $newRank->rank : null;
        $this->season->ranking_updated = Carbon::now();
        $this->season->save();

        // increment the settings
        $this->site->settings->set('ranking.parameters.week', ++$options['week']);

        // if the team is ranked, send the notification
        if ($newRank || $lastRank) {
            $notification = new RankingsUpdated($newRank, $lastRank);
            $this->site->notify($notification);
        }
    }

    /**
     * Get's the ranking for the given query parameters
     * This will either get it from the cache, or load/parse the MWPA for the data, then cache it
     *
     * @param $query
     * @return Ranking
     */
    private function getRankings($query)
    {
        $queryString = http_build_query($query);

        return Cache::remember(self::CACHE_KEY_PREFIX.$queryString, self::CACHE_FOR, function() use ($queryString) {
            return $this->parse($queryString);
        });
    }

    /**
     * Parse the data from the MWPA site and return the Ranking
     *
     * @param $queryString
     * @return Ranking|bool
     */
    private function parse($queryString)
    {
        $url = $this->urlBase . '?' . $queryString;
        $guzzle = $this->guzzleClient();
        $goutte = new GoutteClient();
        $goutte->setClient($guzzle);
        
        $crawler = $goutte->request('GET', $url);

        $rows = $crawler->filterXPath('//table[@class="rankings"]/tr');
        // only header row and empty notice, no rankings out yet
        if (count($rows) === 2) {
            return false;
        }

        $ranking = new Ranking();

        // parse the rows for the ranks
        // skip the heading row
        $len = count($rows);
        for($i = 1; $i < $len; $i++) {
            $row = $rows->eq($i);
            $cells = $row->children();
            $rank = (int) trim($cells->eq(0)->text());
            $team = trim($cells->eq(1)->text());
            $tied = str_contains($team, '(Tie)');

            if ($tied) {
                $team = trim(str_replace('(Tie)', '', $team));
            }

            $ranking->ranks[] = new Rank(compact('rank', 'team', 'tied'));
        }
        
        // parse the heading for the date range
        $heading = $crawler->filter('h2')
            ->reduce(function(Crawler $node) {
               return starts_with($node->text(), 'MWPA Boys Rankings');
            })->text();

        preg_match('/MWPA Boys Rankings - Week (?P<week>\d+) - \((?P<start>\w+ \d{1,2}) - (?P<end>\w+ \d{1,2})\)/', $heading, $matched);
        if(count($matched) > 1) {
            $ranking->week = (int)$matched['week'];
            $ranking->start = Carbon::parse($matched['start']);
            $ranking->end = Carbon::parse($matched['end']);
        }

        return $ranking;
    }


}
