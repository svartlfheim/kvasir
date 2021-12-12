<?php

namespace App\Common\API;

class PaginationData implements JSONSerializableInterface
{
    protected ?string $nextToken = null;
    protected ?string $prevToken = null;
    protected ?array $filters = null;
    protected ?string $orderBy = null;
    protected ?string $orderDirection = null;

    public function withNextToken(string $token): self
    {
        $this->nextToken = $token;

        return $this;
    }

    public function withPreviousToken(string $token): self
    {
        $this->prevToken = $token;

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
            'next_token' => $this->nextToken,
            'prev_token' => $this->prevToken,
            'order' => [
                'field' => $this->orderBy,
                'direction' => $this->orderDirection,
            ],
            'filters' => $this->filters,
        ];
    }
}
