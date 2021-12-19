<?php

namespace App\Tests\Unit\Connections\Handler\Response;

use App\Tests\Unit\TestCase;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\ConnectionList;
use App\Common\API\Error\FieldValidationErrorList;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;

class ListConnectionsResponseTest extends TestCase
{
    public function testGetters(): void
    {
        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockPagination = $this->createMock(PaginationData::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $resp = new ListConnectionsResponse(
            $mockCommand,
            $mockResponseStatus,
            $mockErrors,
            $mockConnList,
            $mockPagination,
        );

        $this->assertSame($mockCommand, $resp->getCommand());
        $this->assertSame($mockResponseStatus, $resp->getStatus());
        $this->assertSame($mockPagination, $resp->getPagination());
        $this->assertSame($mockConnList, $resp->getConnections());
        $this->assertSame($mockErrors, $resp->getErrors());
    }
}
