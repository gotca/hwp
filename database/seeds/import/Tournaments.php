<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Tournaments extends ImportBase
{
    protected $oldTable = 'tournament';

    protected $oldPK = 'tournament_id';

    protected $newTable = 'tournaments';

    protected $addSite = true;

    protected function alter($data)
    {
        return $data;
    }
}
