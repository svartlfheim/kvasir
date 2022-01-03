<?php

namespace App\Common\Generator;

use Ramsey\Uuid\Uuid as BaseUuid;
use Ramsey\Uuid\UuidInterface;

class Uuid
{
    public function generate(): UuidInterface
    {
        return BaseUuid::uuid4();
    }
}
