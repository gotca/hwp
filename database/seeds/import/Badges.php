<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Badges extends ImportBase
{
    protected $oldTable = 'badge';
    protected $oldPK = 'badge_id';

    protected $newTable = 'badges';

    protected $addSite = false;

    protected function alter($data) {

        unset($data->double);

        return $data;
    }
}
