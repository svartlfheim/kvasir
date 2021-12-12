<?php

namespace App\Common\Handler;

class ResponseStatus
{
    public const STATUS_CREATED = 'created';
    public const STATUS_OK = 'ok';
    public const STATUS_VALIDATION_ERROR = 'validation_error';
    public const STATUS_ERROR = 'error';

    public const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_ERROR,
        self::STATUS_OK,
        self::STATUS_VALIDATION_ERROR,
    ];

    protected string $name;

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function newOK(): self
    {
        return new self(
            self::STATUS_OK,
        );
    }

    public static function newCreated(): self
    {
        return new self(
            self::STATUS_CREATED,
        );
    }

    public static function newError(): self
    {
        return new self(
            self::STATUS_ERROR,
        );
    }

    public static function newValidationError(): self
    {
        return new self(
            self::STATUS_VALIDATION_ERROR,
        );
    }

    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
