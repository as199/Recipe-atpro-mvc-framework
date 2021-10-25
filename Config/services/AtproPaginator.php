<?php

namespace Atpro\mvc\Config\services;

class AtproPaginator
{
    protected float $totalPages;
    protected float $offset;
    protected int $page;

    /**
     * @param float $totalRecords
     * @param int $recordsPerPage
     * @param int $page
     */
    public function __construct(float $totalRecords, int $recordsPerPage, int $page)
    {
        $this->totalPages = ceil($totalRecords / $recordsPerPage);
        $this->page = filter_var($page, FILTER_VALIDATE_INT);
        $this->offset = $recordsPerPage * ($this->page - 1);
    }
    public function getOffset(): int
    {
        return (int) $this->offset;
    }
    public function getPage(): int
    {
        return $this->page;
    }
}
