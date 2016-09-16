<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class BadgeSeason extends ImportBase
{
    protected $oldTable = 'season_to_badge';

    protected $newTable = 'badge_season';

    protected $addSite = false;

    protected function alter($data) {
        $this->rename($data, 'awarded', 'created_at');

        return $data;
    }
}
