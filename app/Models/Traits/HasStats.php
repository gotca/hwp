<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/21/2016
 * Time: 11:25 PM
 */

namespace App\Models\Traits;


use App\Models\Stat;
use Illuminate\Support\Facades\DB;

trait HasStats
{

    /**
     * Returns a Stat object that is the sum of all the fields.
     * This uses the stats relationship of whatever it is placed on, so make sure that exists.
     *
     * @return Stat
     */
    public function statsTotal()
    {
        $sum = function($field) {
            return 'SUM(`'.$field.'`) AS `'.$field.'`';
        };

        $fields = Stat::FIELDS;
        $select = array_map($sum, $fields);
        $select = implode(', ', $select);

        return $this->stats()
            ->select(DB::raw($select))
            ->first();
    }

}