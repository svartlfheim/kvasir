<?php

namespace App\Connections\Model\Entity;

use Ramsey\Uuid\UuidInterface;

class Connection
{
    public const ENGINE_MYSQL = 'mysql';
    public const ENGINE_POSTGRES = 'postgresql';

    public const ENGINES = [
        self::ENGINE_MYSQL,
        self::ENGINE_POSTGRES,
    ];

    protected UuidInterface $id;
    protected string $name;
    protected string $engine;

    protected function __construct(UuidInterface $id, string $name, string $engine)
    {
        $this->id = $id;
        $this->name = $name;
        $this->engine = $engine;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public static function create(UuidInterface $id, string $name, string $engine): self
    {
        self::guardEngine($engine);

        return new self(
            $id,
            $name,
            $engine
        );
    }
}
