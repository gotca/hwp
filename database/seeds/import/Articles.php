<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Articles extends ImportBase
{
    protected $oldTable = 'article';

    protected $oldPK = 'article_id';

    protected $newTable = 'articles';

    protected $addSite = true;

    protected function alter($data)
    {
        return $data;
    }
}
