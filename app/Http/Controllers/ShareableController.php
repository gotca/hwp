<?php

namespace App\Http\Controllers;

use App\Exceptions\ShareableHandler;
use App\Models\ActiveSeason;
use App\Models\Game;
use App\Models\Photo;
use App\Models\PlayerSeason;
use App\Models\Stat;
use App\Services\PlayerData\PlayerDataService;
use App\Services\PlayerListService;
use Illuminate\Http\Request;

use App\Http\Requests;

class ShareableController extends Controller
{

    const PATTERN_COLOR = '#2a82c9';
    const PATTERN_COLOR_DARKER = '#435e8d';

    const SQUARE = 'square';
    const RECTANGLE = 'rectangle';

    /**
     * @var PlayerListService
     */
    protected $playerListService;

    /**
     * ShareableController constructor.
     *
     * @param PlayerListService $playerListService
     */
    public function __construct(PlayerListService $playerListService)
    {
        $this->playerListService = $playerListService;

        // set exceptions to be handled by the shareable handler
        \App::singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            ShareableHandler::class
        );
    }

    /**
     * Gets data for the game shareable, with optional player
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function game(Request $request, $shape, $ext = null)
    {
        $dimensions = config('shareable.dimensions.' . $shape);

        $game = $this->getGame($request);
        $player = $this->getPlayer($request);
        $stats = null;
        $charts = null;
        $photo = null;
        $pattern = null;

        if ($player) {
            $stats = $this->getStats($player, $game);
            $photo = $this->getGamePlayerPhoto($game, $player);
            $charts = $this->getCharts($player, $stats ? $stats : new Stat());
        }

        if (!$photo) { $photo = $this->getGamePhoto($game); }
        if ($photo) { $photo->photo = $photo->getPhotoAttribute(); }
        if (!$photo) { $pattern = $this->getPattern($stats ? self::PATTERN_COLOR_DARKER : self::PATTERN_COLOR); }

        $game->opponent = $this->chunkName($game->opponent);
        $game->us = 'Hudsonville' . ($game->team === 'JV' ? ' JV' : '');

        $data = compact('dimensions', 'game', 'player', 'stats', 'charts', 'photo', 'pattern');

        if ($ext == '.svg') {
            return view('shareables.' . $shape . '.game' . ($player ? '-player' : ''), $data);
        }

        return $this->outputData($data);
    }

    /**
     * Gets data for the player shareable
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function player(Request $request, $shape, $ext = null)
    {
        $dimensions = config('shareable.dimensions.' . $shape);

        $player = $this->getPlayer($request);
        $stats = $this->getStats($player);
        $photo = $this->getPlayerPhoto($player);
        $badges = $this->getBadges($player);
        $charts = $this->getCharts($player, $stats ? $stats : new Stat());
        $pattern = null;

        if ($photo) {
            $photo->photo = $photo->getPhotoAttribute();
        } else {
            $pattern = $this->getPattern($stats ? self::PATTERN_COLOR_DARKER : self::PATTERN_COLOR);
        }

        $data = compact('dimensions', 'player', 'stats', 'charts', 'photo', 'pattern', 'badges');

        if ($ext == '.svg') {
            return view('shareables.' . $shape . '.player', $data);
        }

        return $this->outputData($data);
    }

    /**
     * Gets data for the update shareable
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $shape, $ext = null)
    {
        $dimensions = config('shareable.dimensions.' . $shape);

        $update = json_decode($request->data);

        // find player mentions
        $re = '/#\d{1,2}(?:(?:[a-zA-Z]|\/)?\d{0,2})? (?:\b\w+) (?:\b\w+)/';
        $classes = ['mention--yellow', 'mention--grey'];
        preg_match_all($re, $update->msg, $matches, PREG_OFFSET_CAPTURE, 0);
        $mentions = [];
        if (count($matches)) {
            $matches = $matches[0];
            $i = 0;
            foreach($matches as $match) {
                $mentions[] = [
                    'name' => $match[0],
                    'starts' => $match[1],
                    'ends' => $match[1] + strlen($match[0]),
                    'class' => array_key_exists($i, $classes) ? $classes[$i] : ''
                ];
                $i++;
            }
        }

        // split into lines
        $splitLimit = $shape === ShareableController::SQUARE ? 30 : 20;
        $lines = explode('ZZZ', wordwrap($update->msg, $splitLimit, 'ZZZ', false));

        // wrap mentions in the lines with tspans
        if (count($mentions)) {
            $mentions = collect($mentions);

            $open = '<tspan class="mention CLASS">';
            $close = '</tspan>';
            $start = 0;

            foreach($lines as &$line) {
                $len = strlen($line);
                $end = $start + $len;
                $offset = 0;

                $mentions
                    ->filter(function($m) use($start, $end) {
                        return ($m['starts'] >= $start && $m['starts'] <= $end) ||
                            ($m['ends'] >= $start && $m['ends'] <= $end);
                    })
                    ->each(function($m) use ($start, $end, &$line, $open, $close, &$offset) {
                        $o = max($m['starts'] - $start, 0);
                        $c = min($m['ends'] - $start, $end);

                        $classed = str_replace('CLASS', $m['class'], $open);

                        $line = substr_replace($line, $classed, $o + $offset, 0);
                        $offset += strlen($classed);

                        $line = substr_replace($line, $close, $c + $offset, 0);
                        $offset += strlen($close);
                    });

                $start = $end + 1;
            }
        }


        $photo = null;
        $pattern = null;

        $game = $this->getGame($request, $update->game_id);
        $namekeys = $update->mentions;
        $players = collect($namekeys)
            ->map(function($namekey) use ($request) {
                return $this->getPlayer($request, $namekey);
            });

        $players->first(function($player) use (&$photo, $game) {
           $photo = $this->getGamePlayerPhoto($game, $player);
           if ($photo) {
               $photo->photo = $photo->getPhotoAttribute();
           }
           return $photo;
        });

        if (!$photo) {
            $pattern = $this->getPattern(self::PATTERN_COLOR, $update->msg);
        }


        $opponent = $update->opponent;
        $score = $update->score;
        $quarter = $update->quarterTitle;

        $data = compact('dimensions', 'photo', 'pattern', 'lines', 'opponent', 'score', 'quarter');

        if ($ext == '.svg') {
            return view('shareables.' . $shape . '.update', $data);
        }

        return $this->outputData($data);
    }


    protected function getGame(Request $request, $id = null)
    {
        if (!$request->has('game_id') && !$id) {
            return null;
        }

        return Game::with('badge')->findOrFail($id ? $id : $request->input('game_id'));
    }

    protected function getPlayer(Request $request, $namekey = null)
    {
        if (!$request->has('namekey') && !$namekey) {
            return null;
        }

        return $this->playerListService->getPlayerForNameKey($namekey ? $namekey : $request->get('namekey'));
    }

    protected function getStats(PlayerSeason $playerSeason = null, Game $game = null)
    {
        if (!($playerSeason || $game)) {
            return null;
        }

        if (!$game) {
            return $playerSeason->statsTotal();
        } else {
            return $playerSeason->stats()->where('game_id', '=', $game->id)->first();
        }
    }

    protected function getBadges(PlayerSeason $playerSeason)
    {
        return $playerSeason->badges()->orderBy('display_order')->get();
    }

    protected function getGamePlayerPhoto(Game $game, PlayerSeason $playerSeason)
    {
        $album_id = $game->album_id;
        $player_id = $playerSeason->player_id;
        $season_id = $playerSeason->season_id;

        if (!$album_id) {
            return null;
        }

        return Photo::query()
            ->join('photo_player', function($join) use($player_id, $season_id) {
                $join->on('photos.id', '=', 'photo_player.photo_id')
                    ->where('photo_player.player_id', '=', $player_id)
                    ->where('photo_player.season_id', '=', $season_id);
            })
            ->join('album_photo', function($join) use($album_id) {
                $join->on('photos.id', '=', 'album_photo.photo_id')
                    ->where('album_photo.album_id', '=', $album_id);
            })
            ->inRandomOrder()
            ->first();
    }

    protected function getGamePhoto(Game $game)
    {
        if (!$game->album_id) {
            return null;
        }

        return $game->album->photos()->inRandomOrder()->first();
    }

    protected function getPlayerPhoto(PlayerSeason $playerSeason)
    {
        $player_id = $playerSeason->player_id;
        $season_id = $playerSeason->season_id;

        return Photo::query()
            ->join('photo_player', function($join) use($player_id, $season_id) {
                $join->on('photos.id', '=', 'photo_player.photo_id')
                    ->where('photo_player.player_id', '=', $player_id)
                    ->where('photo_player.season_id', '=', $season_id);
            })
            ->inRandomOrder()
            ->first();
    }

    protected function chunkName($name) {
        $name = trim($name);

        if (count($name) < 15 || !strpos($name,  ' ')) {
            return $name;
        }

        $parts = explode(' ', $name);
        if (count($parts) < 3) {
            return $name;
        }

        $chunks = array_chunk($parts, 2);

        return array_map(function($leg) {
            return join(' ', $leg);
        }, $chunks);
    }

    protected function getPattern($color = self::PATTERN_COLOR, $str = null) {
        $pattern = new \RedeyeVentures\GeoPattern\GeoPattern();
        $pattern->setString($str ? $str : time());
        $pattern->setColor($color);

        return $pattern->toDataURI();
    }

    protected function getCharts(PlayerSeason $player, Stat $stats)
    {
        switch ($player->position) {
            case PlayerSeason::GOALIE:
                return $this->makeGoalieCharts($stats);

            case PlayerSeason::FIELD:
                return $this->makeFieldCharts($stats);

            default:
                return [];
        }
    }

    protected function makeFieldCharts($stats)
    {
        $chartData = [];

        # Shooting
        $percent = $stats->shots ? round(($stats->goals / $stats->shots) * 100) : 0;
        $chartData[] =[
            'slices' => [$percent],
            'value' => $percent,
            'suffix' => $percent ? '%' : false,
            'subvalue' => $stats->goals.'/'.$stats->shots,
            'title' => trans('stats.shooting_percent')
        ];

        # Steals/Turnovers
        if ($stats->steals || $stats->turnovers) {
            $total = $stats->steals + $stats->turnovers;
            $negative = $stats->steals < $stats->turnovers;
            $percent = round((
                ($negative ? $stats->turnovers : $stats->steals) / $total
            ) * 100);

            $chartData[] = [
                'negative' => $negative,
                'slices' => [$percent],
                'prefix' => $negative ? '-' : '+',
                'value' => abs($stats->steals_to_turnovers),
                'subvalue' => $stats->steals . '/' . $stats->turnovers,
                'title' => trans('stats.steals').'/'.trans('stats.turnovers')
            ];
        } else {
            $chartData[] = [
                'slices' => [0],
                'value' => 0,
                'subvalue' => '0/0',
                'title' => trans('stats.steals').'/'.trans('stats.turnovers')
            ];
        }

        # Kickouts
        if ($stats->kickouts_drawn || $stats->kickouts) {
            $total = $stats->kickouts_drawn + $stats->kickouts;
            $negative = $stats->kickouts > $stats->kickouts_drawn;
            $percent = round((
                ($negative ? $stats->kickouts : $stats->kickouts_drawn) / $total
            ) * 100);

            $chartData[] = [
                'negative' => $negative,
                'slices' => [$percent],
                'prefix' => $negative ? '-' : '+',
                'value' => abs($stats->kickouts_drawn_to_called),
                'subvalue' => $stats->kickouts_drawn . '/' . $stats->kickouts,
                'title' => trans('stats.kickouts'),
                'subtitle' => trans('stats.drawn').'/'.trans('stats.called')
            ];
        } else {
            $chartData[] = [
                'slices' => [0],
                'value' => '0',
                'subvalue' => '0/0',
                'title' => trans('stats.kickouts'),
                'subtitle' => trans('stats.drawn').'/'.trans('stats.called')
            ];
        }

        # Sprints || Goals/Assists
        if ($stats->sprints_taken > 2) {
            $percent = round($stats->sprints_percent);
            $chartData[] = [
                'slices' => [$percent],
                'value' => $percent,
                'suffix' => '%',
                'subvalue' => $stats->sprints_won . '/' . $stats->sprints_taken,
                'title' => trans('stats.sprints'),
            ];
        } else {
            if ($stats->goals || $stats->assists) {
                $total = $stats->goals + $stats->assists;
                $goals = round((($stats->goals) / $total) * 100);
                $assists = 100 - $goals;

                $chartData[] = [
                    'slices' => [$goals, $assists],
                    'value' => $stats->goals.'/'.$stats->assists,
                    'title' => trans('stats.goals').'/'.trans('stats.assists')
                ];
            } else {
                $chartData[] = [
                    'slices' => [0],
                    'value' => 0,
                    'title' => trans('stats.goals').'/'.trans('stats.assists')
                ];
            }
        }


        return $chartData;
    }

    protected function makeGoalieCharts($stats)
    {
        $chartData = [];

        # Saves
        $chartData[] =[
            'slices' => [$stats->save_percent],
            'value' => round($stats->save_percent),
            'suffix' => '%',
            'subvalue' => $stats->saves.'/'.$stats->goals_allowed,
            'title' => trans('stats.saves')
        ];

        # 5 Meters
       if ($stats->five_meters_taken_on) {
           $stopped = $stats->five_meters_blocked + $stats->five_meters_missed;
           $percent = round(($stopped / $stats->five_meters_taken_on) * 100);
           $ratio = 100 / $stats->five_meters_taken_on;

           $chartData[] = [
               'slices' => [
                   $stats->five_meters_blocked * $ratio,
                   $stats->five_meters_missed * $ratio
               ],
               'value' => $percent,
               'suffix' => '%',
               'subvalue' => $stats->five_meters_blocked.'/'.$stats->five_meters_missed.'/'.$stats->five_meters_allowed,
               'title' => trans('stats.five_meters'),
               'subtitle' => trans('stats.blocked').'/'.trans('stats.missed').'/'.trans('stats.allowed')
           ];
       } else {
           $chartData[] = [
               'value' => '0',
               'subvalue' => '0/0/0',
               'title' => trans('stats.five_meters'),
               'subtitle' => trans('stats.blocked').'/'.trans('stats.missed').'/'.trans('stats.allowed')
           ];
       }

       // # Shoot Out
       if ($stats->shoot_out_taken_on) {
           $stopped = $stats->shoot_out_blocked + $stats->shoot_out_missed;
           $percent = round(($stopped / $stats->shoot_out_taken_on) * 100);
           $ratio = 100 / $stats->shoot_out_taken_on;

           $chartData[] = [
               'slices' => [
                   $stats->shoot_out_blocked * $ratio,
                   $stats->shoot_out_missed * $ratio
               ],
               'value' => $percent,
               'suffix' => '%',
               'subvalue' => $stats->shoot_out_blocked.'/'.$stats->shoot_out_missed.'/'.$stats->shoot_out_allowed,
               'title' => trans('stats.shoot_outs'),
               'subtitle' => trans('stats.blocked').'/'.trans('stats.missed').'/'.trans('stats.allowed')
           ];
       } else {
           $chartData[] = [
               'value' => '0',
               'subvalue' => '0/0/0',
               'title' => trans('stats.shoot_outs'),
               'subtitle' => trans('stats.blocked').'/'.trans('stats.missed').'/'.trans('stats.allowed')
           ];
       }

       # Assists
       if ($stats->goals || $stats->assists) {
           $total = $stats->goals + $stats->assists;
           $goals = round((($stats->goals) / $total) * 100);
           $assists = 100 - $goals;

           $chartData[] = [
               'slices' => [$goals, $assists],
               'value' => $stats->goals.'/'.$stats->assists,
               'title' => trans('stats.goals').'/'.trans('stats.assists')
           ];
       } else {
           $chartData[] = [
               'slices' => [0],
               'value' => 0,
               'title' => trans('stats.goals').'/'.trans('stats.assists')
           ];
       }

        return $chartData;
    }


    protected function outputData($items)
    {
        return response()->json($items);
    }
}
