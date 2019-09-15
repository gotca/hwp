<?gotca

namespace App\Console\Commands;

use App\Events\ArticleImported;
use App\Models\ActiveSeason;
use App\Models\ActiveSite;
use App\Models\Player;
use App\Models\Season;
use App\Models\Site;
use App\Services\PlayerListService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


abstract class ArticleImporter extends LoggedCommand
{

    /**
     * Gets the last ran timestamp and does all the hard work
     *
     * @param int $lastRan
     * @return array [[article ids], # of tags]
     */
    abstract protected function parse($lastRan);

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
     * The key in the settings file for the site
     *
     * @var string
     */
    protected $settingKey;

    /**
     * The url to parse
     *
     * @var string
     */
    protected $url;

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
     * @param ActiveSite $site
     * @param ActiveSeason $season
     * @param PlayerListService $playerListService
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
        $lastRan = $this->site->settings->get($this->settingKey.'.lastRun');

        list($imported_articles, $imported_tags) = $this->parse($lastRan);

        $this->site->settings->set($this->settingKey.'.lastRun', time());
        foreach($imported_articles as $articleId) {
            $event = new ArticleImported($this->site, $this->season, $articleId);
            event($event);    
        }
    }

    /**
     * Setups the pdo prepared statements
     *
     * return void
     */
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
    // Original PHP code by Chirp Internet: www.chirp.com.au
    // Please acknowledge use of this code by including this header.
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
