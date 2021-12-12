<?php

namespace App\Connections\Command;

interface ListConnectionsInterface
{
    public function version(): int;

    public function getLimit(): int;
}
