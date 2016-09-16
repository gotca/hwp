<?php

namespace App\Models\Recent\Render;

use App\Models\Article;

class Articles extends Renderer
{

    /**
     * The blade template to use
     *
     * @var string
     */
    protected $view = 'recent.article';

    /**
     * Process the content and save to $this->data
     *
     * @param $content string
     */
    public function process($content)
    {
        $ids = json_decode($content);
        $article = Article::with('players')
            ->whereIn('id', $ids)
            ->first();

        $this->data = [
            'article' => $article,
            'recent' => $this->recent
        ];
    }

}