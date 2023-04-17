<?php

namespace App\Connections\Command;

interface ListConnectionsInterface
{
    public function version(): int;

    public function getPageSize(): int;

    public function getPage(): int;

    public function getOrderField(): string;

    public function getOrderDirection(): string;
}
