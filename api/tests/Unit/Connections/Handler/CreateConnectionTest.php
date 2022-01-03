<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\Command\CommandValidatorInterface;
use App\Common\Generator\Uuid;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\CreateConnection;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Handler\Response\Factory;
use App\Connections\Model\Entity\Connection;
use App\Connections\Repository\ConnectionsInterface;
use App\Tests\Unit\TestCase;
use Ramsey\Uuid\UuidInterface;

class CreateConnectionTest extends TestCase
{
    public function testSuccessfulHandling(): void
    {
        $cmd = $this->createMock(CreateConnectionInterface::class);
        $cmd->expects($this->exactly(1))->method('getName')->willReturn('test-conn');
        $cmd->expects($this->exactly(1))->method('getEngine')->willReturn(Connection::ENGINE_POSTGRES);

        $mockUuid = $this->createMock(UuidInterface::class);

        $expectedConnection = Connection::create(
            $mockUuid,
            'test-conn',
            Connection::ENGINE_POSTGRES
        );

        $mockResponse = $this->createMock(CreateConnectionResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with($this->isInstanceOf(ResponseStatus::class))->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnection')->with($expectedConnection)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(CreateConnectionResponse::class)->willReturn($mockResponse);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(true);

        $validator = $this->createMock(CommandValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($mockErrors);

        $mockRepo = $this->createMock(ConnectionsInterface::class);
        $mockRepo->expects($this->exactly(1))->method('save')->with($this->isInstanceOf(Connection::class))->willReturn($expectedConnection);


        $mockGenerator = $this->createMock(Uuid::class);
        $mockGenerator->expects($this->exactly(1))->method('generate')->willReturn($mockUuid);

        $handler = new CreateConnection($mockFactory, $validator, $mockRepo, $mockGenerator);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }

    public function testValidationErrorHandling(): void
    {
        $cmd = $this->createMock(CreateConnectionInterface::class);

        $mockResponse = $this->createMock(CreateConnectionResponse::class);
        $mockResponse->expects($this->exactly(1))->method('setCommand')->with($cmd)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setStatus')->with(ResponseStatus::newValidationError())->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setConnection')->with(null)->willReturn($mockResponse);
        $mockResponse->expects($this->exactly(1))->method('setErrors')->with($this->isInstanceOf(FieldValidationErrorList::class))->willReturn($mockResponse);

        $mockFactory = $this->createMock(Factory::class);
        $mockFactory->expects($this->exactly(1))->method('make')->with(CreateConnectionResponse::class)->willReturn($mockResponse);

        $mockErrors = $this->createMock(FieldValidationErrorList::class);
        $mockErrors->expects($this->exactly(1))->method('isEmpty')->willReturn(false);

        $validator = $this->createMock(CommandValidatorInterface::class);
        $validator->expects($this->exactly(1))->method('validate')->with($cmd)->willReturn($mockErrors);

        $mockRepo = $this->createMock(ConnectionsInterface::class);

        $mockGenerator = $this->createMock(Uuid::class);

        $handler = new CreateConnection($mockFactory, $validator, $mockRepo, $mockGenerator);

        $this->assertSame(
            $mockResponse,
            $handler($cmd)
        );
    }
}
