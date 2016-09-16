<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Players extends ImportBase
{
    protected $oldTable = 'player';

    protected $oldPK = 'player_id';

    protected $newTable = 'players';

    protected $addSite = true;

    protected function alter($data)
    {
        $this->rename($data, 'last_update', 'updated_at');
        
        unset($data->alex);
        
        return $data;
    }
}
