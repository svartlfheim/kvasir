<?php

namespace App\Common\Database;

use Countable;
use Iterator;

class SortOrders implements Iterator, Countable
{
    protected int $index = 0;
    protected array $orders;

    protected function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }

    public function count(): int
    {
        return count($this->orders);
    }

    public function current(): mixed
    {
        return $this->orders[$this->index];
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
        return isset($this->orders[$this->key()]);
    }

    public function toArray(): array
    {
        return $this->orders;
    }

    public function reverse(): void
    {
        $this->orders = array_reverse($this->orders);
        $this->rewind();
    }

    public static function new(ColumnSortOrder ...$orders): self
    {
        return new self($orders);
    }
}
