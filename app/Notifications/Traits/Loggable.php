<?php

namespace App\Notifications\Traits;


use Illuminate\Support\Facades\App;

trait Loggable
{

    /**
     * Should we be sending to the log instead of the normal methods?
     *
     * @return bool
     */
    public function sendToLog()
    {
        return env('LOG_NOTIFICATIONS');
    }

}