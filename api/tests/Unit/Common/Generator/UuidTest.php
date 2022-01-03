<?php

namespace App\Tests\Unit\Common\Generator;

use App\Common\Generator\Uuid;
use App\Tests\Unit\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidTest extends TestCase
{
    public function testThatItGeneratesAUuidInterface(): void
    {
        $g = new Uuid();
        $this->assertInstanceOf(UuidInterface::class, $g->generate());
    }
}
