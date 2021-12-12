<?php

namespace App\Connections\DTO\V1;

use App\Common\DTO\FromRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\DTO\CreateConnectionInterface;

class CreateConnection implements FromRequest, CreateConnectionInterface
{
    protected $name;

    protected $engine;

    protected function __construct($name, $engine)
    {
        $this->name = $name;
        $this->engine = $engine;
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self(
            $request->get('name'),
            $request->get('engine')
        );
    }

    public function getName(): string
    {
        return $this->name ?? "";
    }

    public function getEngine(): string
    {
        return $this->engine ?? "";
    }
}
