<?php

namespace App\Connections\Model;

use Iterator;
use Countable;
use App\Connections\Model\Entity\Connection;

class ConnectionList implements Iterator, Countable
{
    protected int $index = 0;
    protected array $connections;

    protected function __construct(array $connections)
    {
        $this->connections = $connections;
    }

    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }

    public function count(): int
    {
        return count($this->connections);
    }

    public function current(): mixed
    {
        return $this->connections[$this->index];
    }
    public function next(): void
    {
        $this->index++;
    }
    public function rewind(): void
    {
        $this->index = 0;
    }
    public function key(): mixed
    {
        return $this->index;
    }
    public function valid(): bool
    {
        return isset($this->connections[$this->key()]);
    }

    public function reverse()
    {
        $this->connections = array_reverse($this->connections);
        $this->rewind();
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
