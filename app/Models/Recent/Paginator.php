<?php
/**
 * Created by PhpStorm.
 * User: Duby
 * Date: 8/16/2016
 * Time: 7:27 PM
 */

namespace App\Models\Recent;


use App\Models\Recent;
use Illuminate\Pagination\AbstractPaginator;

class Paginator extends AbstractPaginator
{

    public $perPageCustom= [0, 9, 13];

    public $perPageDefault = 13;

    protected $hasMore;

    public function __construct($page = null, $pageName = 'page')
    {
        $this->currentPage = $page ?: self::resolveCurrentPage($pageName);
        $this->pageName = $pageName;
        $this->path = self::resolveCurrentPath();

        $take = $this->getTake($this->currentPage);
        $skip = $this->getSkip($this->currentPage);

        $this->items = Recent::latest($this->currentPage)
            ->skip($skip)
            ->take($take + 1) // need an additional for paginator to know there's additional pages
            ->get();

        $this->items->each(function($item) {
            $item->getRenderer();
        });

        $this->perPage = $take;
        $this->hasMore = count($this->items) > ($this->perPage);
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->hasMore;
    }

    

    protected function getTake($page) {
        $idx = $page;
        if (array_key_exists($idx, $this->perPageCustom)) {
            return $this->perPageCustom[$idx];
        } else {
            return $this->perPageDefault;
        }
    }

    protected function getSkip($page) {
        // get the total of custom numbered pages up to $page
        $custom = array_splice($this->perPageCustom, 0, $page);
        $total = array_sum($custom);

        // add in the default for any pages past the custom
        $diff = $page - count($custom);
        $total += $diff * $this->perPageDefault;

        return $total;
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'per_page' => $this->perPage(), 'current_page' => $this->currentPage(),
            'next_page_url' => $this->nextPageUrl(), 'prev_page_url' => $this->previousPageUrl(),
            'from' => $this->firstItem(), 'to' => $this->lastItem(),
            'data' => $this->items->toArray(),
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}