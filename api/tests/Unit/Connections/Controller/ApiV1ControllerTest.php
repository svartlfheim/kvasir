<?php

namespace App\Tests\Unit\Connections\Controller;

use App\Tests\Unit\TestCase;
use App\Common\MessageBusInterface;
use App\Connections\Command\V1\ListConnections;
use App\Connections\Controller\ApiV1Controller;
use App\Connections\Command\V1\CreateConnection;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
use App\Connections\Handler\Response\ListConnectionsResponse;

class ApiV1ControllerTest extends TestCase
{
    public function testListConnections(): void
    {
        $dtoMock = $this->createMock(ListConnections::class);

        $mockCommandResponse = $this->createMock(ListConnectionsResponse::class);
        $mockResponseBuilder = $this->createMock(ListConnectionsJSONResponseBuilder::class);
        $mockResponseBuilder->expects($this->once())
            ->method('fromCommandResponse')
            ->with($mockCommandResponse)
            ->willReturn(new JsonResponse(['some'=> 'data'], 200));

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->once())
            ->method('dispatchAndGetResult')
            ->with($dtoMock, [])
            ->willReturn($mockCommandResponse);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertEquals(
            new JsonResponse(['some'=> 'data'], 200),
            $ctrl->index($dtoMock, $mockResponseBuilder),
        );
    }

    public function testCreateConnection(): void
    {
        $dtoMock = $this->createMock(CreateConnection::class);

        $mockMessageBus = $this->createMock(MessageBusInterface::class);
        $mockMessageBus->expects($this->once())
            ->method('dispatchAndGetResult')
            ->with($dtoMock, [])
            ->willReturn(['some' => 'data']);

        $ctrl = new ApiV1Controller();
        $ctrl->withMessageBus($mockMessageBus);

        $this->assertEquals(
            new JsonResponse([
                'some' => 'data',
            ]),
            $ctrl->create($dtoMock),
        );
    }
}
