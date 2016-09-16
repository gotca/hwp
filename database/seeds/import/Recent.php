<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;

class Recent extends ImportBase
{
    protected $oldTable = 'recent';

    // we're not mapping the ids for this one
    // protected $oldPK = 'recent_id';

    protected $newTable = 'recent';

    protected $addSite = true;

    protected function alter($data) {
        // don't import some things
        if ($data->template === 'changelog' ||
            $data->template === 'ranking' ||
            $data->template === 'plain' // these were done manually
        ) {
            return false;
        }

        if ($data->template === 'plain') {
            $data->template = 'note';
        }

        unset($data->recent_id);
        $this->rename($data, 'inserted', 'created_at');
        $this->rename($data, 'template', 'renderer');
        $data->updated_at = $data->created_at;

        if ($data->renderer === 'photos') {
            $data->content = $this->convertPhotosToID($data->content);
            if ($data->content === false) {
                return false;
            }
        }

        if ($data->renderer === 'articles') {
            $ids = json_decode($data->content);
            if (count($ids) > 1) {
                $arr = [];
                foreach($ids as $id) {
                    $tmp = clone $data;
                    $tmp->content = '['.$id.']';
                    $arr[] = $tmp;
                }
                return $arr;
            }
        }

        return $data;
    }

    private function convertPhotosToID($content)
    {
        $oldIDs = json_decode($content);
        $ids = [];

        foreach($oldIDs as $oldID) {
            $newID = $this->getNewPhotoID($oldID);
            if ($newID !== 1) {
                $ids[] = $newID;
            }
        }

        if (count($ids) === 0) {
            return false;
        }

        return json_encode($ids);
    }
}
