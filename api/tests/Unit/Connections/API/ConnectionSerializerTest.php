<?php

namespace App\Tests\Unit\Connections\API;

use App\Connections\API\ConnectionSerializer;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;

class ConnectionSerializerTest extends TestCase
{
    public function testSerializeConnectionV1(): void
    {
        $conn = $this->createMock(Connection::class);
        $conn->expects($this->once())->method('getName')->willReturn('conn-1');
        $conn->expects($this->once())->method('getEngine')->willReturn('mysql');

        $serializer = new ConnectionSerializer();
        $serializer->setVersion(1);

        $this->assertEquals([
            'name' => 'conn-1',
            'engine' => 'mysql',
        ], $serializer->serialize($conn));
    }

    public function testSerializeConnectionUnsupportedAPIVersion(): void
    {
        $conn = $this->createMock(Connection::class);
        $conn->expects($this->never())->method('getName');
        $conn->expects($this->never())->method('getEngine');

        $serializer = new ConnectionSerializer();
        $serializer->setVersion(2);

        $this->expectExceptionObject(new \RuntimeException("Version 2 not implemented for Connection serialization."));
        $serializer->serialize($conn);
    }
}
