<?php

namespace App\Common\API\Error;

use RuntimeException;

class FieldValidationError
{
    protected string $fieldName;

    protected array $violations;

    protected function __construct(string $fieldName, array $violations)
    {
        $this->fieldName = $fieldName;
        $this->violations = $violations;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getViolations(): array
    {
        return $this->violations;
    }

    public static function new(string $fieldName, array $violations): self
    {
        foreach ($violations as $i => $violation) {
            if (! $violation instanceof Violation) {
                throw new RuntimeException("Message at index '$i' must be an instance of " . Violation::class);
            }
        }

        return new self($fieldName, $violations);
    }
}
