<?php

namespace App\Tests\Unit\Connections\API;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\API\CreateConnectionJSONResponseBuilder;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Model\Entity\Connection;
use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateConnectionJSONResponseBuilderTest extends TestCase
{
    public function testBuildsOKResponse(): void
    {
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->once())->method('isEmpty')->willReturn(true);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->once())->method('version')->willReturn(1);

        $mockConnection = $this->createMock(Connection::class);

        $mockStatus = $this->createMock(ResponseStatus::class);

        $resp = $this->createMock(CreateConnectionResponse::class);
        $resp->expects($this->exactly(2))->method('getErrors')->willReturn($mockErrors);
        $resp->expects($this->exactly(2))->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->exactly(1))->method('getConnection')->willReturn($mockConnection);
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockStatus);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->once())->method('setVersion')->with(1);
        $mockSerializer->expects($this->once())->method('serialize')->with($mockConnection)->willReturn([
            'name' => 'my-conn',
            'engine' => 'mysql',
        ]);

        $expect = new JsonResponse([
            'meta' => [],
            'data' => [
                'name' => 'my-conn',
                'engine' => 'mysql',
            ],
            'errors' => [],
        ], 201);

        $mockHTTPBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPBuilder->expects($this->once())->method('withMeta')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withData')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withStatus')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withHTTPMappedErrors')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('json')->willReturn($expect);

        $builder = new CreateConnectionJSONResponseBuilder($mockSerializer, $mockHTTPBuilder);

        $this->assertEquals($expect, $builder->fromCommandResponse($resp));
    }

    public function testBuildsResponseWithErrors(): void
    {
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->once())->method('isEmpty')->willReturn(false);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->never())->method('version')->willReturn(1);

        $mockStatus = $this->createMock(ResponseStatus::class);

        $resp = $this->createMock(CreateConnectionResponse::class);
        $resp->expects($this->exactly(2))->method('getErrors')->willReturn($mockErrors);
        $resp->expects($this->once())->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->never())->method('getConnection');
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockStatus);

        $mockSerializer = $this->createMock(ConnectionSerializer::class);
        $mockSerializer->expects($this->never())->method('setVersion');
        $mockSerializer->expects($this->never())->method('serialize');

        $expect = new JsonResponse([
            'meta' => [],
            'data' => null,
            'errors' => [
                'error' => 'some_error',
            ],
        ], 201);

        $mockHTTPBuilder = $this->createMock(HTTPResponseBuilder::class);
        $mockHTTPBuilder->expects($this->once())->method('withMeta')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withData')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withStatus')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('withHTTPMappedErrors')->willReturn($mockHTTPBuilder);
        $mockHTTPBuilder->expects($this->once())->method('json')->willReturn($expect);

        $builder = new CreateConnectionJSONResponseBuilder($mockSerializer, $mockHTTPBuilder);


        $this->assertEquals($expect, $builder->fromCommandResponse($resp));
    }
}
