<?php

namespace App\Collections;


use App\Models\Advantage;
use Illuminate\Database\Eloquent\Collection;

class AdvantagesCollection extends Collection
{

    public function us()
    {
        $item = $this->first(function($item) {
            return $item->team == 'US';
        });

        return $item ? $item : new Advantage();
    }

    public function them()
    {
        $item = $this->first(function($item) {
            return $item->team == 'THEM';
        });

        return $item ? $item : new Advantage();
    }

}