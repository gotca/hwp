<?php

namespace App\Collections;


use Illuminate\Database\Eloquent\Collection;

class BoxscoresCollection extends Collection
{

    const MINIMUM_QUARTER = 4;

    public function getQuarters()
    {
        return max($this->max('quarter'), self::MINIMUM_QUARTER);
    }

    public function quarter($quarter)
    {
        return $this->filter(function($item) use ($quarter) {
            return $item->quarter == $quarter;
        });
    }

    public function us()
    {
        return $this->filter(function($item) {
            return $item->team == 'US';
        });
    }

    public function them()
    {
        return $this->filter(function($item) {
            return $item->team == 'THEM';
        });
    }

}