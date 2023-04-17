<?php

namespace App\Connections\API;

use App\Connections\Model\Entity\Connection;
use RuntimeException;

class ConnectionSerializer
{
    protected ?int $version;

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    public function serialize(Connection $conn): array
    {
        switch ($this->version) {
            case 1:
                return $this->serializeConnectionV1($conn);
            default:
                throw new RuntimeException("Version $this->version not implemented for Connection serialization.");
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
