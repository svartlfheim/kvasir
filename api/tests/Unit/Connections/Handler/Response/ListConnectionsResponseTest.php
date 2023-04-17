<?php

namespace App\Tests\Unit\Connections\Handler\Response;

use App\Common\API\ArrayData;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\Metadata;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListConnectionsResponseTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockPagination = $this->createMock(PaginationData::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->assertSame(null, $resp->getCommand());
        $this->assertSame(null, $resp->getStatus());
        $this->assertSame(null, $resp->getConnections());
        $this->assertSame(null, $resp->getErrors());
        $this->assertSame(null, $resp->getPagination());

        $this->assertSame($resp, $resp->setCommand($mockCommand));
        $this->assertSame($resp, $resp->setConnections($mockConnList));
        $this->assertSame($resp, $resp->setStatus($mockResponseStatus));
        $this->assertSame($resp, $resp->setErrors($mockErrors));
        $this->assertSame($resp, $resp->setPagination($mockPagination));

        $this->assertSame($mockCommand, $resp->getCommand());
        $this->assertSame($mockResponseStatus, $resp->getStatus());
        $this->assertSame($mockConnList, $resp->getConnections());
        $this->assertSame($mockErrors, $resp->getErrors());
        $this->assertSame($mockPagination, $resp->getPagination());
    }

    public function testJsonWithNoConnections(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);

        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockPagination = $this->createMock(PaginationData::class);

        $mockConnList = $this->createMock(ConnectionList::class);
        $mockConnList->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $expectMeta = (new Metadata())->withPagination($mockPagination);
        $expect = new JsonResponse();
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withMeta')->with($expectMeta)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withData')->with(new ArrayData([]))->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->with($mockErrors, $mockCommand)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withStatus')->with($mockResponseStatus)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setErrors($mockErrors)
            ->setPagination($mockPagination)
            ->setConnections($mockConnList)
            ->setStatus($mockResponseStatus);

        $this->assertSame($expect, $resp->json());
    }

    public function testJsonWithConnections(): void
    {
        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->exactly(1))->method('setVersion')->with(1);
        $mockSerializer->expects($this->exactly(2))->method('serialize')->withConsecutive([$mockConn1], [$mockConn2])->willReturnOnConsecutiveCalls(['name' => 'conn1'], ['name' => 'conn2']);

        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockCommand->expects($this->exactly(1))->method('version')->willReturn(1);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockPagination = $this->createMock(PaginationData::class);

        $mockConnList = $this->buildMockIterator(ConnectionList::class, [$mockConn1, $mockConn2]);
        $mockConnList->expects($this->exactly(1))->method('isEmpty')->willReturn(false);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $expectMeta = (new Metadata())->withPagination($mockPagination);
        $expect = new JsonResponse();
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withMeta')->with($expectMeta)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withData')->with(new ArrayData([['name' => 'conn1'], ['name' => 'conn2']]))->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->with($mockErrors, $mockCommand)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('withStatus')->with($mockResponseStatus)->willReturn($mockHTTPResponseBuilder);
        $mockHTTPResponseBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setErrors($mockErrors)
            ->setPagination($mockPagination)
            ->setConnections($mockConnList)
            ->setStatus($mockResponseStatus);

        $this->assertSame($expect, $resp->json());
    }

    public function testGuardForPagination(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setErrors($mockErrors)
            ->setConnections($mockConnList)
            ->setStatus($mockResponseStatus);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: pagination'));
        $resp->json();
    }

    public function testGuardForErrors(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockPagination = $this->createMock(PaginationData::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setPagination($mockPagination)
            ->setConnections($mockConnList)
            ->setStatus($mockResponseStatus);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: errors'));
        $resp->json();
    }

    public function testGuardForCommand(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockPagination = $this->createMock(PaginationData::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setErrors($mockErrors)
            ->setPagination($mockPagination)
            ->setConnections($mockConnList)
            ->setStatus($mockResponseStatus);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: cmd'));
        $resp->json();
    }

    public function testGuardForStatus(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockPagination = $this->createMock(PaginationData::class);
        $mockConnList = $this->createMock(ConnectionList::class);
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $resp->setCommand($mockCommand)
            ->setErrors($mockErrors)
            ->setPagination($mockPagination)
            ->setConnections($mockConnList);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: status'));
        $resp->json();
    }

    public function testGuardForAllRequiredProps(): void
    {
        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockHTTPResponseBuilder = $this->createMock(HTTPResponseBuilder::class);

        $resp = new ListConnectionsResponse($mockSerializer, $mockHTTPResponseBuilder);

        $this->expectExceptionObject(new RuntimeException('The following required props were not set for json response: errors, cmd, status, pagination'));

        $resp->json();
    }
}
