<?php

namespace App\Tests\Unit\Connections\API;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListConnectionsJSONResponseBuilderTest extends TestCase
{
    public function testBuildFromCommandResponseNoConnections(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);

        $connList = $this->buildMockIterator(ConnectionList::class, []);
        $connList->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);

        $resp->expects($this->exactly(1))->method('getPagination')->willReturn($pagination);
        $resp->expects($this->exactly(1))->method('getConnections')->willReturn($connList);
        $resp->expects($this->exactly(1))->method('getCommand');
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockResponseStatus);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->exactly(0))->method('setVersion');
        $mockSerializer->expects($this->exactly(0))->method('serialize');

        $expect = new JsonResponse([
            'meta' => [
                'pagination' => [
                    'next_token' => 'sometoken',
                ],
            ],
            'data' => [],
            'errors' => [],
        ], 200);

        $mockHTTPBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withMeta')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withData')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withStatus')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $builder = new ListConnectionsJSONResponseBuilder($mockSerializer, $mockHTTPBuilder);

        $jsonResponse = $builder->fromCommandResponse($resp);

        $this->assertEquals($expect, $jsonResponse);
    }

    public function testBuildFromCommandResponseWithConnections(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);

        $mockConn1 = $this->createMock(Connection::class);
        $mockConn2 = $this->createMock(Connection::class);
        $mockConn3 = $this->createMock(Connection::class);

        $conns = [
            $mockConn1,
            $mockConn2,
            $mockConn3,
        ];
        $connList = $this->buildMockIterator(ConnectionList::class, $conns);
        $connList->expects($this->exactly(1))->method('isEmpty')->willReturn(empty($conns));

        $mockCommand = $this->createMock(ListConnectionsInterface::class);
        $mockCommand->expects($this->exactly(1))->method('version')->willReturn(1);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);

        $resp->expects($this->exactly(1))->method('getPagination')->willReturn($pagination);
        $resp->expects($this->exactly(1))->method('getConnections')->willReturn($connList);
        $resp->expects($this->exactly(2))->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockResponseStatus);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->exactly(1))->method('setVersion')->with(1);
        $mockSerializer->expects($this->exactly(3))->method('serialize')
            ->withConsecutive([$mockConn1], [$mockConn2], [$mockConn3])
            ->willReturnOnConsecutiveCalls(
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
                ]
            );

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

        $mockHTTPBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withMeta')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withData')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withStatus')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $builder = new ListConnectionsJSONResponseBuilder($mockSerializer, $mockHTTPBuilder);

        $jsonResponse = $builder->fromCommandResponse($resp);

        $this->assertEquals($expect, $jsonResponse);
    }

    public function testBuildFromCommandResponseHandlesErrors(): void
    {
        $resp = $this->createMock(ListConnectionsResponse::class);

        $pagination = $this->createMock(PaginationData::class);

        $connList = $this->buildMockIterator(ConnectionList::class, []);
        $connList->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $mockResponseStatus = $this->createMock(ResponseStatus::class);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);

        $resp->expects($this->exactly(1))->method('getPagination')->willReturn($pagination);
        $resp->expects($this->exactly(1))->method('getConnections')->willReturn($connList);
        $resp->expects($this->exactly(1))->method('getCommand');
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockResponseStatus);
        $resp->expects($this->exactly(1))->method('getErrors')->willReturn($mockErrors);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->exactly(0))->method('setVersion');
        $mockSerializer->expects($this->exactly(0))->method('serialize');

        $expect = new JsonResponse([
            'meta' => [
                'pagination' => [
                    'next_token' => 'sometoken',
                ],
            ],
            'data' => [],
            'errors' => ['error' => 'some_error'],
        ], 200);

        $mockHTTPBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withMeta')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withData')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withStatus')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('withHTTPMappedErrors')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->exactly(1))->method('json')->willReturn($expect);

        $builder = new ListConnectionsJSONResponseBuilder($mockSerializer, $mockHTTPBuilder);

        $jsonResponse = $builder->fromCommandResponse($resp);

        $this->assertEquals($expect, $jsonResponse);
    }
}
