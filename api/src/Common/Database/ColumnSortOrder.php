<?php

namespace App\Common\Database;

use RuntimeException;

class ColumnSortOrder
{
    public const DIRECTION_ASC = 'ASC';
    public const DIRECTION_DESC = 'DESC';

    public const DIRECTIONS = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC,
    ];

    protected string $field;
    protected string $direction;

    protected function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    protected static function guardDirection(string $dir): void
    {
        if (! in_array($dir, self::DIRECTIONS)) {
            throw new RuntimeException("Direction must be one of: " . implode(", ", self::DIRECTIONS));
        }
    }

    public static function new(string $field, string $dir)
    {
        self::guardDirection($dir);

        return new self($field, $dir);
    }
}
