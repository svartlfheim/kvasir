<?php

namespace App\Connections\Command;

use App\Connections\DTO\ListConnectionsInterface;

class ListConnections
{
    protected ListConnectionsInterface $dto;

    public function __construct(ListConnectionsInterface $dto)
    {
        $this->dto = $dto;
    }
}
