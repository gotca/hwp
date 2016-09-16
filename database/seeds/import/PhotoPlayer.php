<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class PhotoPlayer extends ImportBase
{
    protected $oldTable = 'player_to_photo';

    protected $newTable = 'photo_player';

    protected $addSite = true;

    protected function alter($data)
    {
        $data->photo_id = $this->getNewPhotoID($data->photo_id);
        return $data;
    }
}
