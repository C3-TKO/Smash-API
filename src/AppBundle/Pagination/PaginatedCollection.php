<?php

namespace AppBundle\Pagination;


class PaginatedCollection
{
    private $items;
    private $total;
    private $count;

    /**
     * PaginatedCollection constructor.
     * @param $items
     * @param $total
     */
    public function __construct($items, $total)
    {
        $this->items = $items;
        $this->total = $total;
        $this->count= count($this->items);
    }


}