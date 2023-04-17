<?php

namespace App\Tests\Integration\Connections\Repository;

use App\Common\Database\ColumnSortOrder;
use App\Common\Database\ListOptions;
use App\Common\Database\Pagination;
use App\Common\Database\SortOrders;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Connections\Repository\Connections;
use App\Connections\Repository\ConnectionsInterface;
use App\Tests\Integration\TestCase;
use Doctrine\DBAL\Connection as DBConnection;
use Ramsey\Uuid\Uuid;

class ConnectionsTest extends TestCase
{
    protected function createConnections(int $total): array
    {
        $connections = [];

        // The default pagination size when fetching all is 100
        for ($i = 0; $i < $total; $i++) {
            // Choose a random engine from the array
            $engineKey = rand(
                0,
                count(Connection::ENGINES) - 1
            );

            $connections[] = Connection::create(Uuid::uuid4(), 'connection-' . $i, Connection::ENGINES[$engineKey]);
        }

        return $connections;
    }

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

    public function testSaveAndById(): void
    {
        $uuidVal = "f9ea8f10-fa6b-40bc-bfe5-e650af76abc2";
        $uuid = Uuid::fromString($uuidVal);
        $conn = Connection::create($uuid, 'my-connection', Connection::ENGINE_POSTGRES);

        $repo = $this->getService(ConnectionsInterface::class);

        $repo->save($conn);

        $found = $repo->byId($uuid);

        $this->assertEquals((string) $conn->getId(), $found->getId());
        $this->assertEquals($conn->getEngine(), $found->getEngine());
        $this->assertEquals($conn->getName(), $found->getName());
    }

    public function testByIdForNonExistentConnection(): void
    {
        $uuidVal = "f9ea8f10-fa6b-40bc-bfe5-e650af76abc2";
        $uuid = Uuid::fromString($uuidVal);

        $repo = $this->getService(ConnectionsInterface::class);
        $found = $repo->byId($uuid);

        $this->assertNull($found);
    }

    public function testListingWithoutPaginationOrSorting(): void
    {
        $connections = $this->createConnections(200);

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $found = $repo->all(new ListOptions());

        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $connections[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($connections[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($connections[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }

    public function testListingWithDescNameSort(): void
    {
        $connections = [
            Connection::create(Uuid::uuid4(), 'connection-1', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-2', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-3', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-4', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-5', Connection::ENGINE_MYSQL),
        ];

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $opts = new ListOptions();
        $opts->setSortOrders(SortOrders::new(ColumnSortOrder::new('name', ColumnSortOrder::DIRECTION_DESC)));
        $found = $repo->all($opts);

        $this->assertInstanceOf(ConnectionList::class, $found);

        // They should be sorted the opposite way they were created
        $connections = array_reverse($connections);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $connections[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($connections[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($connections[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }

    public function testListingWithDescNameSortAndEngineSort(): void
    {
        $connections = [
            Connection::create(Uuid::uuid4(), 'connection-1', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-2', Connection::ENGINE_POSTGRES),
            Connection::create(Uuid::uuid4(), 'connection-3', Connection::ENGINE_POSTGRES),
            Connection::create(Uuid::uuid4(), 'connection-4', Connection::ENGINE_MYSQL),
            Connection::create(Uuid::uuid4(), 'connection-5', Connection::ENGINE_MYSQL),
        ];

        // Sorted firs by engine asc (mysql before postgres)
        // Then by the name desc (descending index)
        $expectedOrder = [
            $connections[4],
            $connections[3],
            $connections[0],
            $connections[2],
            $connections[1],
        ];
        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $opts = new ListOptions();
        $opts->setSortOrders(
            SortOrders::new(
                ColumnSortOrder::new('engine', ColumnSortOrder::DIRECTION_ASC),
                ColumnSortOrder::new('name', ColumnSortOrder::DIRECTION_DESC),
            )
        );
        $found = $repo->all($opts);

        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $expectedOrder[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($expectedOrder[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($expectedOrder[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }

    public function testListingForFirstPageOnly(): void
    {
        $connections = $this->createConnections(200);

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $opts = new ListOptions();
        $opts->setPagination(new Pagination(1, 30));

        $found = $repo->all($opts);

        $expectedConnections = array_slice($connections, 0, 30);
        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $expectedConnections[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }

    public function testListingForThirdPage(): void
    {
        $connections = $this->createConnections(200);

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $opts = new ListOptions();
        $opts->setPagination(new Pagination(3, 30));

        $found = $repo->all($opts);

        $expectedConnections = array_slice($connections, 60, 30);
        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $expectedConnections[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }

    public function testListingForFirstPageOnlyWithDescNameSort(): void
    {
        $connections = $this->createConnections(200);

        $repo = $this->getService(ConnectionsInterface::class);

        foreach ($connections as $connection) {
            $repo->save($connection);
        }

        $opts = new ListOptions();
        $opts->setPagination(new Pagination(1, 30))
            ->setSortOrders(SortOrders::new(ColumnSortOrder::new('name', ColumnSortOrder::DIRECTION_DESC)));

        $found = $repo->all($opts);

        $expectedConnections = array_slice(
            array_reverse($connections),
            0,
            30
        );
        $this->assertInstanceOf(ConnectionList::class, $found);

        foreach ($found as $i => $dbConn) {
            $this->assertEquals((string) $expectedConnections[$i]->getId(), (string) $dbConn->getId(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getEngine(), $dbConn->getEngine(), "Error in row: $i");
            $this->assertEquals($expectedConnections[$i]->getName(), $dbConn->getName(), "Error in row: $i");
        }
    }
}
