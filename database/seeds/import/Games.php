<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Games extends ImportBase
{
    protected $oldTable = 'game';

    protected $oldPK = 'game_id';

    protected $newTable = 'games';

    protected $addSite = true;

    protected function alter($data)
    {
        unset($data->bus_time, $data->json_dump, $data->dump_version);
        return $data;
    }
}
