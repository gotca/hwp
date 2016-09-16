<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class AlbumPhoto extends ImportBase
{
    protected $oldTable = 'photo_to_album';

    protected $newTable = 'album_photo';

    protected $addSite = true;

    protected function alter($data)
    {
        $data->photo_id = $this->getNewPhotoID($data->photo_id);

        return $data;
    }
}
