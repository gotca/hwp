<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class BadgePlayer extends ImportBase
{
    protected $oldTable = 'player_to_badge';

    protected $newTable = 'badge_player';

    protected $addSite = true;

    protected function alter($data)
    {

        $this->rename($data, 'awarded', 'created_at');
        
        return $data;
    }
}
