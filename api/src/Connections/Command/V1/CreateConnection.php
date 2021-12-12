<?php

namespace App\Connections\Command\V1;

use App\Common\Command\FromRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\Command\CreateConnectionInterface;

class CreateConnection implements FromRequestInterface, CreateConnectionInterface
{
    protected $name;

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
        return $this->name ?? "";
    }

    public function getEngine(): string
    {
        return $this->engine ?? "";
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self(
            $request->get('name'),
            $request->get('engine')
        );
    }
}
