<?php

namespace App\Connections\Repository;

use App\Common\Database\ListOptions;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use Ramsey\Uuid\UuidInterface;

interface ConnectionsInterface
{
    public function save(Connection $conn): Connection;

    public function byId(UuidInterface $conn): ?Connection;

    public function all(ListOptions $options): ConnectionList;
}
