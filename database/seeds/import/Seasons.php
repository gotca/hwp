<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Seasons extends ImportBase
{
    protected $oldTable = 'season';

    protected $oldPK = 'season_id';

    protected $newTable = 'seasons';

    protected $addSite = true;

    protected function alter($data) {
        unset($data->team_image_map);

        return $data;
    }
}
