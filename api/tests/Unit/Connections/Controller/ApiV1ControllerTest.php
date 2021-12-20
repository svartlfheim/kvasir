<?php

namespace App\Tests\Unit\Connections\Controller;

use App\Common\MessageBusInterface;
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
        $mockCommandResponse->expects($this->exactly(1))->method('json')->willReturn($expected);

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->exactly(1))
            ->method('dispatchAndGetResult')
            ->with($cmdMock, [])
            ->willReturn($mockCommandResponse);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertSame($expected, $ctrl->index($cmdMock));
    }

    public function testCreateConnection(): void
    {
        $expected = new JsonResponse(['some'=> 'data'], 200);
        $mockCommandResponse = $this->createMock(CreateConnectionResponse::class);
        $mockCommandResponse->expects($this->exactly(1))->method('json')->willReturn($expected);

        $cmdMock = $this->createMock(CreateConnection::class);

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->exactly(1))
            ->method('dispatchAndGetResult')
            ->with($cmdMock, [])
            ->willReturn($mockCommandResponse);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertSame($expected, $ctrl->create($cmdMock));
    }
}
