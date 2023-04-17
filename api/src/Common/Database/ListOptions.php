<?php

namespace App\Common\Database;

class ListOptions
{
    public const DEFAULT_PAGE_SIZE = 20;
    public const DEFAULT_PAGE = 1;

    protected ?Pagination $pagination;
    protected SortOrders $sort;

    public function __construct()
    {
        $this->resetPagination();
        $this->resetSortOrders();
    }

    public function addSortOrder(ColumnSortOrder $order): self
    {
        $orders = $this->sort->toArray();
        $orders[] = $order;

        $this->sort = SortOrders::new(...$orders);

        return $this;
    }

    public function resetSortOrders(): self
    {
        $this->sort = SortOrders::new();

        return $this;
    }

    public function setSortOrders(SortOrders $orders): self
    {
        $this->sort = $orders;

        return $this;
    }

    public function getSortOrders(): SortOrders
    {
        return $this->sort;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    public function resetPagination(): self
    {
        $this->pagination = null;

        return $this;
    }

    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }
}
