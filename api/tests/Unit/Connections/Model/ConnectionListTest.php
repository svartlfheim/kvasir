<?php

namespace App\Tests\Unit\Connections\Model;

use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use Countable;
use DateTime;
use Iterator;

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

    public function testConvertsToArray(): void
    {
        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);
        $conns = [
            $mockConn1,
            $mockConn2,
        ];
        $connList = ConnectionList::fromArray($conns);

        $this->assertEquals($conns, $connList->toArray());
    }

    public function testAddAnotherList(): void
    {
        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);
        $mockConn3 = $this->createMock(Connection::class);
        $mockConn4 = $this->createMock(Connection::class);
        $conns1 = [
            $mockConn1,
            $mockConn2,
        ];
        $conns2 = [
            $mockConn3,
            $mockConn4,
        ];
        $connList1 = ConnectionList::fromArray($conns1);
        $connList2 = ConnectionList::fromArray($conns2);

        $connList1->add($connList2);
        $this->assertEquals([
            $mockConn1,
            $mockConn2,
            $mockConn3,
            $mockConn4,
        ], $connList1->toArray());
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
