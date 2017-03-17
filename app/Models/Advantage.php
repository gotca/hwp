<?php

namespace App\Models;

use HipsterJazzbo\Landlord\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Advantage extends Model
{
    use BelongsToTenant;

    /**
     * Specify the tenant columns to use for this model
     * This always ignores the season tenant check
     *
     * @var array
     */
    protected $tenantColumns = ['site_id'];

    public function game()
    {
        return $this->belongsTo('App\Models\Game');
    }

    public function newCollection(array $models = [])
    {
        return new \App\Collections\AdvantagesCollection($models);
    }
}
