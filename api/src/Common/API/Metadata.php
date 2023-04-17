<?php

namespace App\Common\API;

class Metadata implements JSONSerializableInterface
{
    protected array $fields;

    public function __construct()
    {
        $this->fields = [];
    }

    public function withPagination(PaginationData $pagination): self
    {
        $this->fields['pagination'] = $pagination;

        return $this;
    }

    public function withField(string $key, JSONSerializableInterface $value): self
    {
        $this->fields[$key] = $value;

        return $this;
    }

    public function toJSON(): array
    {
        $val = [];

        foreach ($this->fields as $k => $v) {
            $val[$k] = $v->toJSON();
        }

        return $val;
    }
}
