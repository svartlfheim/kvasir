<?php

namespace App\Common\Database;

class Pagination
{
    protected int $page;
    protected int $pageSize;

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function calculateOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }
}
