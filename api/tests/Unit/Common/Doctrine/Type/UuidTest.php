<?php

namespace App\Tests\Unit\Common\Doctrine\Type;

use App\Common\Doctrine\Type\Uuid;
use App\Tests\Unit\TestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Uuid as BaseUuid;
use Ramsey\Uuid\UuidInterface;

class UuidTest extends TestCase
{
    public function testThatItConvertsToDatabaseValue(): void
    {
        $t = new Uuid();

        $uuid = BaseUuid::uuid4();

        $platform = $this->createMock(AbstractPlatform::class);

        $val = $t->convertToDatabaseValue($uuid, $platform);

        $this->assertEquals((string) $uuid, $val);
    }

    public function testThatItConvertsToPHPValue(): void
    {
        $t = new Uuid();

        $uuid = "d8a75e02-db56-4b79-9667-f631ba9827ac";

        $platform = $this->createMock(AbstractPlatform::class);

        $val = $t->convertToPHPValue($uuid, $platform);

        $this->assertInstanceOf(UuidInterface::class, $val);
        $this->assertEquals($uuid, (string) $val);
    }

    public function testThatItHasTheCorrectName(): void
    {
        $t = new Uuid();

        $this->assertEquals('uuid', $t->getName());
    }
}
