<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class GameUpdateDumps extends ImportBase
{
    protected $oldTable = 'live_scoring_dump';

    protected $oldPK = 'dump_id';

    protected $newTable = 'game_update_dumps';

    protected $addSite = true;

    protected function alter($data)
    {
        return $data;
    }
}
