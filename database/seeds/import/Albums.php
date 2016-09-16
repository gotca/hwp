<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Albums extends ImportBase
{
    protected $oldTable = 'photo_album';

    protected $oldPK = 'album_id';

    protected $newTable = 'albums';

    protected $addSite = true;

    protected function alter($data)
    {
        $data->cover_id = $this->getNewPhotoID($data->cover_photo_id);
        $this->rename($data, 'modified', 'created_at');
        $data['updated_at'] = $data['created_at'];

        unset($data->cover_photo_id);

        return $data;
    }
}
