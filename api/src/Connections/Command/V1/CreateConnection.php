<?php

namespace App\Connections\Command\V1;

use App\Common\Command\FromRequestInterface;
use App\Common\Validation\UniqueRecord;
use App\Common\Validation\UniqueRecordMode;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Model\Entity\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueRecord(
    entityClass: Connection::class,
    mode: UniqueRecordMode::CREATE,
    fields: ['name'],
)]
class CreateConnection implements FromRequestInterface, CreateConnectionInterface
{
    /** @todo: Add an integration tests for the validation */

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $name;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $engine;

    protected function __construct($name, $engine)
    {
        $this->name = $name;
        $this->engine = $engine;
    }

    public function version(): int
    {
        return 1;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function getEngine(): string
    {
        return $this->engine ?? '';
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self(
            $request->get('name'),
            $request->get('engine')
        );
    }
}
