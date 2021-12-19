<?php

namespace App\Tests\Unit\Connections\API;

use RuntimeException;
use App\Tests\Unit\TestCase;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\Entity\Connection;
use App\Common\API\Error\FieldValidationErrorList;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\API\CreateConnectionJSONResponseBuilder;
use App\Connections\Handler\Response\CreateConnectionResponse;

class CreateConnectionJSONResponseBuilderTest extends TestCase
{
    public function testBuildsOKResponse(): void
    {
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(2))->method('isEmpty')->willReturn(true);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->once())->method('version')->willReturn(1);

        $mockConnection = $this->createMock(Connection::class);
        $mockConnection->expects($this->once())->method('getName')->willReturn('my-conn');
        $mockConnection->expects($this->once())->method('getEngine')->willReturn('mysql');

        $mockStatus = $this->createMock(ResponseStatus::class);
        $mockStatus->expects($this->once())->method('__toString')->willReturn(ResponseStatus::STATUS_CREATED);

        $resp = $this->createMock(CreateConnectionResponse::class);
        $resp->expects($this->exactly(2))->method('getErrors')->willReturn($mockErrors);
        $resp->expects($this->exactly(1))->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->exactly(1))->method('getConnection')->willReturn($mockConnection);
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockStatus);

        $builder = new CreateConnectionJSONResponseBuilder();

        $expect = new JsonResponse([
            'meta' => [],
            'data' => [
                'name' => 'my-conn',
                'engine' => 'mysql',
            ],
            'errors' => [],
        ], 201);
        $this->assertEquals($expect, $builder->fromCommandResponse($resp));
    }

    public function testBuildsResponseWithErrors(): void
    {
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(2))->method('isEmpty')->willReturn(false);
        $mockErrors->expects($this->once())->method('toJSON')->willReturn(['error' => 'some_error']);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->never())->method('version')->willReturn(1);

        $mockStatus = $this->createMock(ResponseStatus::class);
        $mockStatus->expects($this->once())->method('__toString')->willReturn(ResponseStatus::STATUS_CREATED);

        $resp = $this->createMock(CreateConnectionResponse::class);
        $resp->expects($this->exactly(2))->method('getErrors')->willReturn($mockErrors);
        $resp->expects($this->never())->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->never())->method('getConnection');
        $resp->expects($this->exactly(1))->method('getStatus')->willReturn($mockStatus);

        $builder = new CreateConnectionJSONResponseBuilder();

        $expect = new JsonResponse([
            'meta' => [],
            'data' => null,
            'errors' => [
                'error' => 'some_error',
            ],
        ], 201);
        $this->assertEquals($expect, $builder->fromCommandResponse($resp));
    }

    public function testExceptionIsThrownForUnsupportedAPIVersion(): void
    {
        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->once())->method('isEmpty')->willReturn(true);

        $mockCommand = $this->createMock(CreateConnectionInterface::class);
        $mockCommand->expects($this->once())->method('version')->willReturn(2);

        $mockConnection = $this->createMock(Connection::class);
        $mockConnection->expects($this->never())->method('getName')->willReturn('my-conn');
        $mockConnection->expects($this->never())->method('getEngine')->willReturn('mysql');

        $mockStatus = $this->createMock(ResponseStatus::class);
        $mockStatus->expects($this->never())->method('__toString')->willReturn(ResponseStatus::STATUS_CREATED);

        $resp = $this->createMock(CreateConnectionResponse::class);
        $resp->expects($this->once())->method('getErrors')->willReturn($mockErrors);
        $resp->expects($this->exactly(1))->method('getCommand')->willReturn($mockCommand);
        $resp->expects($this->exactly(1))->method('getConnection')->willReturn($mockConnection);
        $resp->expects($this->never())->method('getStatus')->willReturn($mockStatus);

        $builder = new CreateConnectionJSONResponseBuilder();

        $this->expectExceptionObject(new RuntimeException("Version 2 not implemented for Connection serialization."));
        $builder->fromCommandResponse($resp);
    }
}
