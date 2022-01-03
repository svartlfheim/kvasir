<?php

namespace App\Common\API\Error;

use App\Common\API\JSONSerializableInterface;
use Countable;
use Iterator;

class FieldValidationErrorList implements Iterator, Countable, JSONSerializableInterface
{
    protected int $index = 0;
    protected array $errors;

    protected function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function isEmpty(): bool
    {
        return $this->count() == 0;
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function current(): mixed
    {
        return $this->errors[$this->index];
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
        return isset($this->errors[$this->key()]);
    }

    public function reverse(): void
    {
        $this->errors = array_reverse($this->errors);
        $this->rewind();
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function add(FieldValidationError $error): void
    {
        $this->errors[] = $error;
    }

    public function toJSON(): array
    {
        $data = [];

        foreach ($this->errors as $error) {
            $data[$error->getFieldName()] = array_map(function (Violation $violation): array {
                return [
                    'rule' => $violation->getRule(),
                    'message' => $violation->getMessage(),
                ];
            }, $error->getViolations());
        }

        return $data;
    }
}
