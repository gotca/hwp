<?php

namespace App\Models\Recent\Render;

use App\Models\Photo;

class Photos extends Renderer
{
    const BG_LIMIT = 20;

    /**
     * The blade template to use
     *
     * @var string
     */
    protected $view = 'recent.photos';

    /**
     * Process the content and save to $this->data
     *
     * @param $content string
     */
    public function process($content)
    {
        $photo_ids = json_decode($content);
        $count = count($photo_ids);
        $photos = Photo::with('players')
            ->whereIn('id', array_slice($photo_ids, 0, self::BG_LIMIT))
            ->get();

        $this->data = [
            'count' => $count,
            'photos' => $photos,
            'recent' => $this->recent
        ];
    }

}