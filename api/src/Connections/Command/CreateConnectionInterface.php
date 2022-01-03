<?php

namespace App\Connections\Command;

interface CreateConnectionInterface
{
    public function version(): int;

    public function getName(): string;

    public function getEngine(): string;
}
