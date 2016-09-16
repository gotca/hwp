<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Stats extends ImportBase
{
    protected $oldTable = 'stat';

    protected $oldPK = 'stat_id';

    protected $newTable = 'stats';

    protected $addSite = true;

    protected function alter($data)
    {
        $this->rename($data, 'turn_overs', 'turnovers');
        return $data;
    }
}
