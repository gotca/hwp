<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/16/2016
 * Time: 9:09 PM
 */

namespace App\Models\Traits;


use App\Collections\CustomCollection;

trait UsesCustomCollection
{

    public function newCollection(array $models = [])
    {
        return new CustomCollection($models);
    }
}