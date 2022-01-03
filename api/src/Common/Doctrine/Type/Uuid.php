<?php

namespace App\Common\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Ramsey\Uuid\Uuid as BaseUuid;

class Uuid extends GuidType
{
    public const TYPE = 'uuid';

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return BaseUuid::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return (string) $value;
    }

    public function getName(): string
    {
        return self::TYPE;
    }
}
