<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/13/2016
 * Time: 1:56 PM
 */

namespace App\Database\Seeds\Import;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class ImportBase extends Seeder
{
    /**
     * @var string The old table name
     */
    protected $oldTable;

    /**
     * @var string The PK field name on the old table
     */
    protected $oldPK;

    /**
     * @var string The new table name
     */
    protected $newTable;

    /**
     * @var string The PK field on the new table
     */
    protected $newPK = 'id';

    /**
     * @var bool Should we add the site column, default true
     */
    protected $addSite = true;

    /**
     * @var int string The site id to use
     */
    protected $siteID = 1;

    /**
     * @var array maps old photo ids to the new ones
     */
    private $photoFileToID;


    /**
     *  Runs the seed
     */
    public function run()
    {
        $oldData = DB::connection('mysql.old')->table($this->oldTable)->get();

        foreach ($oldData as $old) {
            $new = $this->alter($old);
            if ($new === false) {
                continue;
            }

            if(!is_array($new)) {
                $new = [$new];
            }

            foreach($new as $item) {
                if ($this->addSite) {
                    $this->addSiteToData($item);
                }

                if ($this->oldPK) {
                    $this->renamePK($item);
                }

                DB::connection('mysql.new')->table($this->newTable)->insert(get_object_vars($item));
            }
        }
    }

    /**
     * Does whatever altering needs to happen to go from old to new
     *
     * @param $data
     * @return mixed
     */
    protected function alter($data)
    {
        return $data;
    }

    /**
     * Adds the supplied site_id to the data
     *
     * @param $data
     */
    protected function addSiteToData($data)
    {
        $data->site_id = $this->siteID;
    }

    /**
     * Renames the primary key field
     *
     * @param $data
     */
    protected function renamePK($data)
    {
        $this->rename($data, $this->oldPK, $this->newPK);
    }

    protected function rename($data, $from, $to) {
        $data->{$to} = $data->{$from};
        unset($data->{$from});
    }

    /**
     * Old photo_id are now the file field, so map from one to the other
     *
     * @param $oldID
     * @return int the new id
     */
    protected function getNewPhotoID($oldID)
    {
        if (!count($this->photoFileToID)) {
            $photos = DB::connection('mysql.new')
                ->table('photos')
                ->select('id', 'file')
                ->get();

            foreach($photos as $photo) {
                $this->photoFileToID[$photo->file] = $photo->id;
            }
        }

        if (array_key_exists($oldID, $this->photoFileToID)) {
            return $this->photoFileToID[$oldID];
        } else {
            return 1;
        }
    }

}