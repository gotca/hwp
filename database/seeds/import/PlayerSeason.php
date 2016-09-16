<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class PlayerSeason extends ImportBase
{
    protected $oldTable = 'player_to_season';

    protected $newTable = 'player_season';

    protected $addSite = true;

    protected function alter($data)
    {
        return $data;
    }
}
