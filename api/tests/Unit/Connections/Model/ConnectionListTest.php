<?php

namespace App\Tests\Unit\Connections\Model;

use DateTime;
use Iterator;
use Countable;
use RuntimeException;
use App\Tests\Unit\TestCase;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;

class ConnectionListTest extends TestCase
{
    public function testImplementsCorrectInterfaces(): void
    {
        $implemented = class_implements(ConnectionList::class);

        $this->assertContains(Countable::class, $implemented);
        $this->assertContains(Iterator::class, $implemented);
    }

    public function testEmptyConstruct(): void
    {
        $connList = ConnectionList::empty();

        $this->assertEquals(0, count($connList));
    }

    public function testConstructFromArray(): void
    {
        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);
        $connList = ConnectionList::fromArray([
            $mockConn1,
            $mockConn2,
        ]);

        $this->assertEquals(2, count($connList));
    }

    public function testConstructFromArrayIsGuarded(): void
    {
        $mockConn = $this->createMock(Connection::class);
        $mockDT = new DateTime();
        $this->expectExceptionObject(new \RuntimeException("Object for key '1' is not a connection, got DateTime"));

        ConnectionList::fromArray([
            $mockConn,
            $mockDT,
        ]);
    }

    public function testItIsIterable(): void
    {
        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);
        $conns = [
            $mockConn1,
            $mockConn2,
        ];
        $connList = ConnectionList::fromArray($conns);

        foreach ($connList as $i => $conn) {
            $this->assertSame($conns[$i], $conn);
        }
    }
}
