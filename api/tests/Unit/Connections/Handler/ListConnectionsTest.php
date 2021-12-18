<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Tests\Unit\TestCase;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\ConnectionList;
use App\Connections\Handler\ListConnections;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;

class ListConnectionsTest extends TestCase
{
    public function testItIsInvokable(): void
    {
        $cmd = $this->createMock(ListConnectionsInterface::class);
        $handler = new ListConnections();

        $resp = $handler($cmd);
        $this->assertInstanceOf(ListConnectionsResponse::class, $resp);
        $this->assertEquals(ResponseStatus::newOK(), $resp->getStatus());
        $this->assertEquals(ConnectionList::empty(), $resp->getConnections());
        $this->assertSame($cmd, $resp->getCommand());
        $this->assertEquals(
            (new PaginationData())->withOrderBy('name', 'ASC'),
            $resp->getPagination(),
        );
    }
}
