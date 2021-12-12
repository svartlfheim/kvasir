<?php

namespace App\Connections\Model\Entity;

class Connection
{
    public const ENGINE_MYSQL = 'mysql';
    public const ENGINE_POSTGRES = 'postgresql';

    public const ENGINES = [
        self::ENGINE_MYSQL,
        self::ENGINE_POSTGRES,
    ];

    protected string $name;
    protected string $engine;

    protected function __construct(string $name, string $engine)
    {
        $this->name = $name;
        $this->engine = $engine;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public static function guardEngine(string $engine): void
    {
        if (!in_array($engine, self::ENGINES)) {
            throw new \RuntimeException("Unknown engine '$engine' for connection.");
        }
    }

    public static function create(string $name, string $engine): self
    {
        self::guardEngine($engine);

        return new self(
            $name,
            $engine
        );
    }
}
