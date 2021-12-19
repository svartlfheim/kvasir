<?php

namespace App\Connections\Command\V1;

use App\Common\Command\FromRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\Command\CreateConnectionInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
