<?php

namespace App\Models;


/**
 * Easy way for dependency inject to get the season being viewed
 * Note - This is the actively viewed season, not the current season
 *
 * Class ActiveSeason
 * @package App\Models
 */
class ActiveSeason extends Season
{
    protected $table = 'seasons';
}
