<?php

namespace App\Common\API;

class PaginationData implements JSONSerializableInterface
{
    protected ?int $page = null;
    protected ?int $pageSize = null;
    protected ?array $filters = [];
    protected ?string $orderBy = null;
    protected ?string $orderDirection = null;

    public function withPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function withPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /* Fine for now, probably an object later */
    public function withFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function withOrderBy(string $field, string $direction): self
    {
        $this->orderBy = $field;
        $this->orderDirection = $direction;

        return $this;
    }

    public function toJSON(): array
    {
        return [
            'page' => $this->page,
            'page_size' => $this->pageSize,
            'order' => [
                'field' => $this->orderBy,
                'direction' => $this->orderDirection,
            ],
            'filters' => $this->filters,
        ];
    }
}
