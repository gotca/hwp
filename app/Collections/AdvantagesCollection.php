<?php

namespace App\Collections;


use Illuminate\Database\Eloquent\Collection;

class AdvantagesCollection extends Collection
{

    public function us()
    {
        return $this->first(function($item) {
            return $item->team == 'US';
        });
    }

    public function them()
    {
        return $this->first(function($item) {
            return $item->team == 'THEM';
        });
    }

}