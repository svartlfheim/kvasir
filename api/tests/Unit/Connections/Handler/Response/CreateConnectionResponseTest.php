<?php

namespace App\Tests\Unit\Connections\Handler\Response;

use App\Tests\Unit\TestCase;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\Entity\Connection;
use App\Common\API\Error\FieldValidationErrorList;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;

class CreateConnectionResponseTest extends TestCase
{
    public function testGetters(): void
    {
        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockConn = $this->createMock(Connection::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $resp = new CreateConnectionResponse(
            $mockCommand,
            $mockResponseStatus,
            $mockErrors,
            $mockConn,
        );

        $this->assertSame($mockCommand, $resp->getCommand());
        $this->assertSame($mockResponseStatus, $resp->getStatus());
        $this->assertSame($mockConn, $resp->getConnection());
        $this->assertSame($mockErrors, $resp->getErrors());
    }
}
