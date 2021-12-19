<?php

namespace App\Tests\Unit\Connections\Model\Entity;

use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use RuntimeException;

class ConnectionTest extends TestCase
{
    public function testCreateAndGetters(): void
    {
        $conn = Connection::create('myconn', Connection::ENGINE_MYSQL);

        $this->assertEquals('myconn', $conn->getName());
        $this->assertEquals(Connection::ENGINE_MYSQL, $conn->getEngine());
    }

    public function testCreateMySQL(): void
    {
        $conn = Connection::create('myconn', Connection::ENGINE_MYSQL);

        $this->assertEquals(Connection::ENGINE_MYSQL, $conn->getEngine());
    }

    public function testCreatePostgres(): void
    {
        $conn = Connection::create('myconn', Connection::ENGINE_POSTGRES);

        $this->assertEquals(Connection::ENGINE_POSTGRES, $conn->getEngine());
    }

    public function testEngineGuard(): void
    {
        $this->expectExceptionObject(new RuntimeException("Unknown engine 'fakeengine' for connection."));
        $conn = Connection::create('myconn', 'fakeengine');
    }
}
