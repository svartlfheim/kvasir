<?php

namespace App\Connections\DTO;

interface CreateConnectionInterface
{
    public function getName(): string;

    public function getEngine(): string;
}
