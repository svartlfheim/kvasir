<?php

namespace App\Connections\Command;

use App\Connections\DTO\CreateConnectionInterface;

class CreateConnection
{
    protected CreateConnectionInterface $dto;

    public function __construct(CreateConnectionInterface $dto)
    {
        $this->dto = $dto;
    }
}
