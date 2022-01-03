<?php

namespace App\Common\API;

/**
 * This is just a generic container for data.
 * It is used to simplify some api serialization.
 */
class ArrayData implements JSONSerializableInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toJSON(): array
    {
        return $this->data;
    }
}
