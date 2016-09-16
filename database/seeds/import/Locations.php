<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Locations extends ImportBase
{
    protected $oldTable = 'location';
    
    protected $oldPK = 'location_id';

    protected $newTable = 'locations';

    protected $addSite = true;

    protected function alter($data)
    {
        $data->title_short = $data->title;
        return $data;
    }
}
