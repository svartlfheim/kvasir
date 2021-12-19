<?php

namespace App\Connections\API;

use App\Connections\Model\Entity\Connection;
use RuntimeException;

trait SerializesConnections
{
    protected function serializeConnectionForVersion(int $version, Connection $conn): array
    {
        switch ($version) {
            case 1:
                return $this->serializeConnectionV1($conn);
            default:
                throw new RuntimeException("Version $version not implemented for Connection serialization.");
        }
    }

    protected function serializeConnectionV1(Connection $conn): array
    {
        return [
            'name' => $conn->getName(),
            'engine' => $conn->getEngine(),
        ];
    }
}
