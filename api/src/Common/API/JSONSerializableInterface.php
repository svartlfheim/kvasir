<?php

namespace App\Common\API;

interface JSONSerializableInterface
{
    public function toJSON(): array;
}
