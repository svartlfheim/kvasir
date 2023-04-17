<?php

namespace App\Tests\Unit\Connections\Handler\Response;

use App\Common\API\ArrayData;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\Metadata;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateConnectionResponseTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockConn = $this->createMock(Connection::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->assertSame(null, $resp->getCommand());
        $this->assertSame(null, $resp->getStatus());
        $this->assertSame(null, $resp->getConnection());
        $this->assertSame(null, $resp->getErrors());

        $this->assertSame($resp, $resp->setCommand($mockCommand));
        $this->assertSame($resp, $resp->setConnection($mockConn));
        $this->assertSame($resp, $resp->setStatus($mockResponseStatus));
        $this->assertSame($resp, $resp->setErrors($mockErrors));

        $this->assertSame($mockCommand, $resp->getCommand());
        $this->assertSame($mockResponseStatus, $resp->getStatus());
        $this->assertSame($mockConn, $resp->getConnection());
        $this->assertSame($mockErrors, $resp->getErrors());
    }

    public function testJsonWithNoErrors(): void
    {
        $mockConn = $this->createMock(Connection::class);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->exactly(1))->method('setVersion')->with(1);
        $mockSerializer->expects($this->exactly(1))->method('serialize')->with($mockConn)->willReturn(['fake' => 'data']);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->exactly(1))->method('version')->willReturn(1);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $expect = new JsonResponse();

        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withMeta')->with($this->isInstanceOf(Metadata::class))->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withData')->with(new ArrayData(['fake' => 'data']))->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->with($mockErrors, $mockCommand)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withStatus')->with($mockResponseStatus)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setConnection($mockConn)
            ->setStatus($mockResponseStatus)
            ->setErrors($mockErrors);

        $this->assertSame($expect, $resp->json());
    }

    public function testJsonWithErrors(): void
    {
        $mockConn = $this->createMock(Connection::class);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(false);

        $expect = new JsonResponse();

        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withMeta')->with($this->isInstanceOf(Metadata::class))->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withData')->with(null)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->with($mockErrors, $mockCommand)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withStatus')->with($mockResponseStatus)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setConnection($mockConn)
            ->setStatus($mockResponseStatus)
            ->setErrors($mockErrors);

        $this->assertSame($expect, $resp->json());
    }

    public function testGuardForErrors(): void
    {
        $mockConn = $this->createMock(Connection::class);
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: errors'));

        // Errors is not set
        $resp->setCommand($mockCommand)
            ->setConnection($mockConn)
            ->setStatus($mockResponseStatus);

        $resp->json();
    }

    public function testGuardForStatus(): void
    {
        $mockConn = $this->createMock(Connection::class);
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: status'));

        // Status is not set
        $resp->setCommand($mockCommand)
            ->setConnection($mockConn)
            ->setErrors($mockErrors);

        $resp->json();
    }

    public function testGuardForCommnd(): void
    {
        $mockConn = $this->createMock(Connection::class);
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: cmd'));

        // Command is not set
        $resp->setStatus($mockResponseStatus)
            ->setConnection($mockConn)
            ->setErrors($mockErrors);

        $resp->json();
    }

    public function testGuardForAllRequiredProps(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new CreateConnectionResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: errors, cmd, status'));

        $resp->json();
    }
}
