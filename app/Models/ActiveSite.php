<?php

namespace App\Models;


/**
 * Easy way for dependency inject to get the site being viewed
 *
 * Class ActiveSite
 * @package App\Models
 */
class ActiveSite extends Site
{
    protected $table = 'sites';
}
