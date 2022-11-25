<?php

namespace App\Common\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UniqueRecord extends Constraint
{
    public const MODE_CREATE = 'create';
    public const MODE_UPDATE = 'update';

    public string $message = 'Another record exists with these values: [{{ string }}]';

    // When checking an update, we need to exclude the id
    public ?string $existingIDFunc = null;

    // Map DTO fields to class properties
    public array $mappings = [];

    public string $entityClass;
    public UniqueRecordMode $mode;
    public array $fields;

    public function __construct(
        string $entityClass,
        UniqueRecordMode $mode,
        array $fields = [],
        array $mappings = [],
        string $existingIDFunc = null,
        array $groups = null,
        $payload = null,
    ) {
        parent::__construct([
            'entityClass' => $entityClass,
            'mode' => $mode,
            'fields' => $fields,
        ], $groups, $payload);

        $this->mode = $mode;
        $this->entityClass = $entityClass;
        $this->existingIDFunc = $existingIDFunc;
        $this->fields = $fields;
        $this->mappings = $mappings;
    }

    public function getRequiredOptions(): array
    {
        return [
            'entityClass',
            'mode',
            'fields',
        ];
    }

    public function getDefaultOption(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string|array
    {
        return [
            self::CLASS_CONSTRAINT,
        ];
    }
}
