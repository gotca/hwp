<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thetispro\Setting\Facades\Setting;

class Site extends Model
{
    protected $settings;

    public function scopeDomain($query, $domain)
    {
        return $query->where('domain', '=', $domain);
    }

    public function getSettingsAttribute()
    {
        if (!isset($this->settings)) {
            $this->settings = Setting::filename($this->getSettingFileName());
        }

        return $this->settings;
    }

    protected function getSettingFileName()
    {
        return $this->domain . '.json';
    }
}
