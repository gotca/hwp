<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class ArticlePlayer extends ImportBase
{
    protected $oldTable = 'player_to_article';

    protected $newTable = 'article_player';

    protected $addSite = true;

    protected function alter($data)
    {
        return $data;
    }
}
