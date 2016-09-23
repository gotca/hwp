<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Photos extends ImportBase
{
    protected $oldTable = 'photo';

    protected $newTable = 'photos';

    protected $addSite = true;

    protected function alter($data)
    {
        $this->rename($data, 'photo_id', 'file');
        $this->rename($data, 'created', 'created_at');
        $data->season_id = $data->file[1];
        $data->shutterfly_node_id = substr($data->file, strpos($data->file, "p") + 1);

        unset($data->viewed);

        return $data;
    }
}
