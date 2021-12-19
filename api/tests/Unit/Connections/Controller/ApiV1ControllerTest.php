<?php

namespace App\Tests\Unit\Connections\Controller;

use App\Common\MessageBusInterface;
use App\Connections\API\CreateConnectionJSONResponseBuilder;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
use App\Connections\Command\V1\CreateConnection;
use App\Connections\Command\V1\ListConnections;
use App\Connections\Controller\ApiV1Controller;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiV1ControllerTest extends TestCase
{
    public function testListConnections(): void
    {
        $expected = new JsonResponse(['some'=> 'data'], 200);
        $cmdMock = $this->createMock(ListConnections::class);

        $mockCommandResponse = $this->createMock(ListConnectionsResponse::class);
        $mockResponseBuilder = $this->createMock(ListConnectionsJSONResponseBuilder::class);
        $mockResponseBuilder->expects($this->exactly(1))
            ->method('fromCommandResponse')
            ->with($mockCommandResponse)
            ->willReturn($expected);

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->exactly(1))
            ->method('dispatchAndGetResult')
            ->with($cmdMock, [])
            ->willReturn($mockCommandResponse);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertSame($expected, $ctrl->index($cmdMock, $mockResponseBuilder));
    }

    public function testCreateConnection(): void
    {
        $expected = new JsonResponse(['some'=> 'data'], 200);
        $mockCommandResponse = $this->createMock(CreateConnectionResponse::class);
        $mockResponseBuilder = $this->createMock(CreateConnectionJSONResponseBuilder::class);
        $mockResponseBuilder->expects($this->exactly(1))
            ->method('fromCommandResponse')
            ->with($mockCommandResponse)
            ->willReturn($expected);

        $cmdMock = $this->createMock(CreateConnection::class);

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->exactly(1))
            ->method('dispatchAndGetResult')
            ->with($cmdMock, [])
            ->willReturn($mockCommandResponse);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertSame($expected, $ctrl->create($cmdMock, $mockResponseBuilder));
    }
}
