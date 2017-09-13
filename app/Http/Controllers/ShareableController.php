<?php

namespace App\Http\Controllers;

use App\Exceptions\ShareableHandler;
use App\Models\ActiveSeason;
use App\Models\Contracts\Shareable;
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

        $game->us = 'Hudsonville' . ($game->team === 'JV' ? ' JV' : '');

        $data = compact('dimensions', 'game', 'player', 'stats', 'charts', 'photo', 'pattern');

        if ($ext == '.svg') {
            return $this->outputSVG('shareables.' . $shape . '.game' . ($player ? '-player' : ''), $data);
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
            return $this->outputSVG('shareables.' . $shape . '.player', $data);
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

        $photo = null;
        $game = $this->getGame($request);
        $namekeys = json_decode($request->mentions);
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

        if (!$photo) { $photo = $this->getGamePhoto($game); }
        if ($photo) { $photo->photo = $photo->getPhotoAttribute(); }

        $data = compact('dimensions', 'game', 'photo');

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
            'title' => trans('shareables.shooting_percent')
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
                'title' => trans('shareables.steals').'/'.trans('shareables.turnovers')
            ];
        } else {
            $chartData[] = [
                'slices' => [0],
                'value' => 0,
                'subvalue' => '0/0',
                'title' => trans('shareables.steals').'/'.trans('shareables.turnovers')
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
                'title' => trans('shareables.kickouts'),
                'subtitle' => trans('shareables.drawn').'/'.trans('shareables.called')
            ];
        } else {
            $chartData[] = [
                'slices' => [0],
                'value' => '0',
                'subvalue' => '0/0',
                'title' => trans('shareables.kickouts'),
                'subtitle' => trans('shareables.drawn').'/'.trans('shareables.called')
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
                'title' => trans('shareables.sprints'),
            ];
        } else {
            if ($stats->goals || $stats->assists) {
                $total = $stats->goals + $stats->assists;
                $goals = round((($stats->goals) / $total) * 100);
                $assists = 100 - $goals;

                $chartData[] = [
                    'slices' => [$goals, $assists],
                    'value' => $stats->goals.'/'.$stats->assists,
                    'title' => trans('shareables.goals').'/'.trans('shareables.assists')
                ];
            } else {
                $chartData[] = [
                    'slices' => [0],
                    'value' => 0,
                    'title' => trans('shareables.goals').'/'.trans('shareables.assists')
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
            'title' => trans('shareables.saves')
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
               'title' => trans('shareables.five_meters'),
               'subtitle' => trans('shareables.blocked').'/'.trans('shareables.missed').'/'.trans('shareables.allowed')
           ];
       } else {
           $chartData[] = [
               'value' => '0',
               'subvalue' => '0/0/0',
               'title' => trans('shareables.five_meters'),
               'subtitle' => trans('shareables.blocked').'/'.trans('shareables.missed').'/'.trans('shareables.allowed')
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
               'title' => trans('shareables.shoot_outs'),
               'subtitle' => trans('shareables.blocked').'/'.trans('shareables.missed').'/'.trans('shareables.allowed')
           ];
       } else {
           $chartData[] = [
               'value' => '0',
               'subvalue' => '0/0/0',
               'title' => trans('shareables.shoot_outs'),
               'subtitle' => trans('shareables.blocked').'/'.trans('shareables.missed').'/'.trans('shareables.allowed')
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
               'title' => trans('shareables.goals').'/'.trans('shareables.assists')
           ];
       } else {
           $chartData[] = [
               'slices' => [0],
               'value' => 0,
               'title' => trans('shareables.goals').'/'.trans('shareables.assists')
           ];
       }

        return $chartData;
    }


    protected function outputData($items)
    {
        return response()->json($items);
    }

    protected function outputSVG($view, $data) {
        return response()
            ->view($view, $data, 200)
            ->header('Content-Type', 'image/svg+xml');
    }
}
