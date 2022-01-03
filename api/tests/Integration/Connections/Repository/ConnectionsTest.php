<?php

namespace App\Tests\Integration\Connections\Repository;

use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Connections\Repository\Connections;
use App\Connections\Repository\ConnectionsInterface;
use App\Tests\Integration\TestCase;
use Doctrine\DBAL\Connection as DBConnection;
use Ramsey\Uuid\Uuid;

class ConnectionsTest extends TestCase
{
    public function testRepoIsLoadedByInterfaceFromContainer(): void
    {
        $this->assertInstanceOf(
            Connections::class,
            $this->getService(ConnectionsInterface::class)
        );
    }

    public function testSave(): void
    {
        $uuidVal = "f9ea8f10-fa6b-40bc-bfe5-e650af76abc2";
        $uuid = Uuid::fromString($uuidVal);
        $conn = Connection::create($uuid, 'my-connection', Connection::ENGINE_POSTGRES);

        $repo = $this->getService(ConnectionsInterface::class);

        $returned = $repo->save($conn);

        $this->assertEquals((string) $conn->getId(), $returned->getId());
        $this->assertEquals($conn->getEngine(), $returned->getEngine());
        $this->assertEquals($conn->getName(), $returned->getName());

        /** @var DBConnection */
        $conn = $this->getService(DBConnection::class);
        $res = $conn->executeQuery("SELECT * FROM connections;");
        $dbRows = $res->fetchAllAssociative();

        $this->assertCount(1, $dbRows);
        $this->assertEquals($uuidVal, $dbRows[0]['id']);
        $this->assertEquals('my-connection', $dbRows[0]['name']);
        $this->assertEquals(Connection::ENGINE_POSTGRES, $dbRows[0]['engine']);
    }

    public function testSaveAndFetch(): void
    {
        $uuidVal = "f9ea8f10-fa6b-40bc-bfe5-e650af76abc2";
        $uuid = Uuid::fromString($uuidVal);
        $conn = Connection::create($uuid, 'my-connection', Connection::ENGINE_POSTGRES);

        $repo = $this->getService(ConnectionsInterface::class);

        $repo->save($conn);

        $found = $repo->byId($uuid);

        $this->assertEquals((string) $conn->getId(), $found->getId());
        $this->assertEquals($conn->getEngine(), $found->getEngine() . 'xx');
        $this->assertEquals($conn->getName(), $found->getName());
    }

    public function testListing(): void
    {
        // Thee are alphabetically ordered by the name, more test will be needed later when we introduce proper filtering, pagination, and sorting options
        $connections = [
            Connection::create(Uuid::fromString("aacbf067-1f77-4214-b049-f4b8264ea7ae"), 'connection-1', Connection::ENGINE_POSTGRES),
            Connection::create(Uuid::fromString("7330d5f4-f2bb-4c92-9c99-431ee4fcb77a"), 'connection-2', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::fromString("fe61ddd0-5b20-48b8-b9bc-6ab73c688825"), 'connection-3', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::fromString("221f8b19-c9e4-4213-8088-a0bb161d22d4"), 'connection-4', Connection::ENGINE_POSTGRES),
            Connection::create(Uuid::fromString("b21e62dd-cf90-4e87-9644-cc03e978e006"), 'connection-5', Connection::ENGINE_POSTGRES),
        ];

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $found = $repo->all();

        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $connections[$i]->getId(), $dbConn->getId());
            $this->assertEquals($connections[$i]->getEngine(), $dbConn->getEngine());
            $this->assertEquals($connections[$i]->getName(), $dbConn->getName());
        }
    }
}
