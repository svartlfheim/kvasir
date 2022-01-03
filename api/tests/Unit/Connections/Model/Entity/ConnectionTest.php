<?php

namespace App\Tests\Unit\Connections\Model\Entity;

use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

class ConnectionTest extends TestCase
{
    public function testCreateAndGetters(): void
    {
        $mockUuid = $this->createMock(UuidInterface::class);
        $conn = Connection::create($mockUuid, 'myconn', Connection::ENGINE_MYSQL);

        $this->assertEquals($mockUuid, $conn->getId());
        $this->assertEquals('myconn', $conn->getName());
        $this->assertEquals(Connection::ENGINE_MYSQL, $conn->getEngine());
    }

    public function testCreateMySQL(): void
    {
        $conn = Connection::create(
            $this->createMock(UuidInterface::class),
            'myconn',
            Connection::ENGINE_MYSQL
        );

        $this->assertEquals(Connection::ENGINE_MYSQL, $conn->getEngine());
    }

    public function testCreatePostgres(): void
    {
        $conn = Connection::create(
            $this->createMock(UuidInterface::class),
            'myconn',
            Connection::ENGINE_POSTGRES
        );

        $this->assertEquals(Connection::ENGINE_POSTGRES, $conn->getEngine());
    }

    public function testEngineGuard(): void
    {
        $this->expectExceptionObject(new RuntimeException("Unknown engine 'fakeengine' for connection."));
        Connection::create(
            $this->createMock(UuidInterface::class),
            'myconn',
            'fakeengine'
        );
    }
}
