<?php

namespace App\Connections\Model;

use App\Connections\Model\Entity\Connection;

// Should implement iterable and such
class ConnectionList
{
    protected array $connections;

    protected function __construct(array $connections)
    {
        $this->connections = $connections;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function fromArray(array $connections): self
    {
        foreach ($connections as $k => $conn) {
            if (! $conn instanceof Connection) {
                $argClass = get_class($conn);

                throw new \RuntimeException("Object for key '$k' is not a connection, got $argClass");
            }
        }

        return new self($connections);
    }
}
