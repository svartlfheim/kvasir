<?php

namespace App\Tests\Unit\Connections\API;

use ArrayIterator;
use App\Tests\Unit\TestCase;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Common\API\Error\FieldValidationErrorList;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
use App\Connections\Handler\Response\ListConnectionsResponse;

class ListConnectionsJSONResponseBuilderTest extends TestCase
{
    public function testBuildFromCommandResponseNoConnections(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);
        $pagination->expects($this->once())
            ->method('toJSON')
            ->willReturn([
                'next_token' => 'sometoken',
            ]);

        $resp->expects($this->once())
            ->method('getPagination')
            ->willReturn($pagination);

        $connList = $this->buildMockIterator(ConnectionList::class, []);
        $connList->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $resp->expects($this->once())
            ->method('getConnections')
            ->willReturn($connList);

        $resp->expects($this->never())
            ->method('getCommand');

        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockResponseStatus->expects($this->once())
            ->method('__toString')
            ->willReturn(ResponseStatus::STATUS_OK);

        $resp->expects($this->once())
            ->method('getStatus')
            ->willReturn($mockResponseStatus);

        $builder = new ListConnectionsJSONResponseBuilder();

        $jsonResponse = $builder->fromCommandResponse($resp);

        $expect = new JsonResponse([
            'meta' => [
                'pagination' => [
                    'next_token' => 'sometoken',
                ],
            ],
            'data' => [],
            'errors' => [],
        ], 200);

        $this->assertEquals($expect, $jsonResponse);
    }

    public function testBuildFromCommandResponseWithConnections(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);
        $pagination->expects($this->once())
            ->method('toJSON')
            ->willReturn([
                'next_token' => 'sometoken',
            ]);

        $resp->expects($this->once())
            ->method('getPagination')
            ->willReturn($pagination);

        $mockConn1 = $this->createMock(Connection::class);
        $mockConn1->expects($this->once())
            ->method('getName')
            ->willReturn('conn-1');
        $mockConn1->expects($this->once())
            ->method('getEngine')
            ->willReturn('mysql');

        $mockConn2 = $this->createMock(Connection::class);
        $mockConn2->expects($this->once())
            ->method('getName')
            ->willReturn('conn-2');
        $mockConn2->expects($this->once())
            ->method('getEngine')
            ->willReturn('postgresql');


        $mockConn3 = $this->createMock(Connection::class);
        $mockConn3->expects($this->once())
            ->method('getName')
            ->willReturn('conn-3');
        $mockConn3->expects($this->once())
            ->method('getEngine')
            ->willReturn('mysql');

        $conns = [
            $mockConn1,
            $mockConn2,
            $mockConn3,
        ];
        $connList = $this->buildMockIterator(ConnectionList::class, $conns);
        $connList->expects($this->once())
            ->method('isEmpty')
            ->willReturn(empty($conns));

        $resp->expects($this->once())
            ->method('getConnections')
            ->willReturn($connList);

        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockCommand->expects($this->once())
            ->method('version')
            ->willReturn(1);

        $resp->expects($this->once())
            ->method('getCommand')
            ->willReturn($mockCommand);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockResponseStatus->expects($this->once())
            ->method('__toString')
            ->willReturn(ResponseStatus::STATUS_OK);

        $resp->expects($this->once())
            ->method('getStatus')
            ->willReturn($mockResponseStatus);

        $builder = new ListConnectionsJSONResponseBuilder();

        $jsonResponse = $builder->fromCommandResponse($resp);

        $expect = new JsonResponse([
            'meta' => [
                'pagination' => [
                    'next_token' => 'sometoken',
                ],
            ],
            'data' => [
                [
                    'name' => 'conn-1',
                    'engine' => 'mysql',
                ],
                [
                    'name' => 'conn-2',
                    'engine' => 'postgresql',
                ],
                [
                    'name' => 'conn-3',
                    'engine' => 'mysql',
                ],
            ],
            'errors' => [],
        ], 200);

        $this->assertEquals($expect, $jsonResponse);
    }

    public function testBuildFromCommandResponseForUnsupportedAPIVersion(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);
        $pagination->expects($this->never())->method('toJSON');

        $resp->expects($this->once())
            ->method('getPagination')
            ->willReturn($pagination);

        $mockConn1 = $this->createMock(Connection::class);
        $mockConn1->expects($this->never())->method('getName');
        $mockConn1->expects($this->never())->method('getEngine');

        $conns = [
            $mockConn1,
        ];

        $connList = $this->buildMockIterator(ConnectionList::class, $conns);
        $connList->expects($this->once())
            ->method('isEmpty')
            ->willReturn(empty($conns));

        $resp->expects($this->once())
            ->method('getConnections')
            ->willReturn($connList);

        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockCommand->expects($this->once())
            ->method('version')
            ->willReturn(2);

        $resp->expects($this->once())
            ->method('getCommand')
            ->willReturn($mockCommand);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockResponseStatus->expects($this->never())->method('__toString');

        $resp->expects($this->never())->method('getStatus');

        $builder = new ListConnectionsJSONResponseBuilder();

        $this->expectExceptionObject(new \RuntimeException("Version 2 not implemented for Connection serialization."));
        $builder->fromCommandResponse($resp);
    }

    public function testBuildFromCommandResponseHandlesErrors(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);
        $pagination->expects($this->once())
            ->method('toJSON')
            ->willReturn([
                'next_token' => 'sometoken',
            ]);

        $resp->expects($this->once())
            ->method('getPagination')
            ->willReturn($pagination);

        $connList = $this->buildMockIterator(ConnectionList::class, []);
        $connList->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $resp->expects($this->once())
            ->method('getConnections')
            ->willReturn($connList);

        $resp->expects($this->never())
            ->method('getCommand');

        $mockResponseStatus = $this->createMock(ResponseStatus::class);
        $mockResponseStatus->expects($this->once())
            ->method('__toString')
            ->willReturn(ResponseStatus::STATUS_OK);

        $resp->expects($this->once())
            ->method('getStatus')
            ->willReturn($mockResponseStatus);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->once())
            ->method('toJSON')
            ->willReturn(['error' => 'some_error']);
        $resp->expects($this->once())
            ->method('getErrors')
            ->willReturn($mockErrors);

        $builder = new ListConnectionsJSONResponseBuilder();

        $jsonResponse = $builder->fromCommandResponse($resp);

        $expect = new JsonResponse([
            'meta' => [
                'pagination' => [
                    'next_token' => 'sometoken',
                ],
            ],
            'data' => [],
            'errors' => ['error' => 'some_error'],
        ], 200);

        $this->assertEquals($expect, $jsonResponse);
    }
}
