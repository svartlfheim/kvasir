<?php

namespace App\Tests\Unit\Connections\API;

use App\Connections\API\SerializesConnections;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;

class SerializesConnectionsTest extends TestCase
{
    protected function getTestObject(): mixed
    {
        $testClass =  new class () {
            use SerializesConnections;

            public function serialize(int $version, Connection $conn): array
            {
                return $this->serializeConnectionForVersion($version, $conn);
            }
        };

        return new $testClass();
    }

    public function testSerializeConnectionV1(): void
    {
        $conn = $this->createMock(Connection::class);
        $conn->expects($this->once())
            ->method('getName')
            ->willReturn('conn-1');
        $conn->expects($this->once())
            ->method('getEngine')
            ->willReturn('mysql');

        $serializer = $this->getTestObject();

        $this->assertEquals([
            'name' => 'conn-1',
            'engine' => 'mysql',
        ], $serializer->serialize(1, $conn));
    }

    public function testSerializeConnectionUnsupportedAPIVersion(): void
    {
        $conn = $this->createMock(Connection::class);
        $conn->expects($this->never())->method('getName');
        $conn->expects($this->never())->method('getEngine');

        $serializer = $this->getTestObject();

        $this->expectExceptionObject(new \RuntimeException("Version 2 not implemented for Connection serialization."));
        $serializer->serialize(2, $conn);
    }
}
