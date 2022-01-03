<?php

namespace App\Connections\Repository;

use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;

interface ConnectionsInterface
{
    public function save(Connection $conn): Connection;

    public function all(): ConnectionList;
}
