<?php

namespace App\Models\Recent\Render;

use App\Models\Tournament as TournamentModel;

class Tournament extends Renderer
{

    /**
     * The blade template to use
     *
     * @var string
     */
    protected $view = 'recent.tournament';

    /**
     * Process the content and save to $this->data
     *
     * @param $content string
     */
    public function process($content)
    {
        $ids = json_decode($content);
        $id = array_pop($ids);

        $tournament = TournamentModel::with('games')->find($id);
        $this->data = [
            'tournament' => $tournament,
            'recent' => $this->recent
        ];
    }

}