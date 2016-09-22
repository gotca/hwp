<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 9/21/2016
 * Time: 10:18 PM
 */

namespace App\Console\Commands\Traits;

use Fusonic\OpenGraph\Consumer AS OpenGraphConsumer;

trait HasOGPhoto
{

    protected function getPhoto($url)
    {
        $consumer = new OpenGraphConsumer();
        try {
            $data = $consumer->loadUrl($url);
            if (property_exists($data, 'images')
                && count($data->images)
                && $data->images[0]->url !== 'None'
            ) {
                return $data->images[0]->url;
            } else {
                return '';
            }

        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            return '';
        }
    }

}