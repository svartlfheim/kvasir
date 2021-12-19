<?php

namespace App\Connections\Command;

interface ListConnectionsInterface
{
    public function version(): int;

    public function getPageSize(): int;

    public function getPage(): string;

    public function getOrderField(): string;

    public function getOrderDirection(): string;
}
