<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 9/21/2016
 * Time: 11:31 PM
 */

namespace App\Events\Contracts;


interface Recent
{
    /**
     * Get the value for site_id
     *
     * @return integer
     */
    public function getSiteId();

    /**
     * Get the value for season_id
     *
     * @return integer
     */
    public function getSeasonId();

    /**
     * Get the value for renderer
     *
     * @return string
     */
    public function getRenderer();

    /**
     * Get the value for content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get the value for sticky
     *
     * @return boolean
     */
    public function getSticky();

}