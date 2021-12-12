<?php

namespace App\Tests\Unit\Connections\Controller;

use PHPUnit\Framework\TestCase;
use App\Connections\Command\V1\ListConnections;
use App\Connections\Controller\ApiV1Controller;
use App\Connections\Command\V1\CreateConnection;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Common\MessageBusInterface;

class ApiV1ControllerTest extends TestCase
{
    public function testListConnections(): void
    {
        $dtoMock = $this->createMock(ListConnections::class);

        $messageBusMock = $this->createMock(MessageBusInterface::class);
        $messageBusMock->expects($this->once())
            ->method('dispatchAndGetResult')
            ->with($dtoMock, [])
            ->willReturn(['some' => 'data']);

        $ctrl = new ApiV1Controller($messageBusMock);

        $this->assertEquals(
            new JsonResponse([
                'some' => 'data',
            ]),
            $ctrl->index($dtoMock),
        );
    }

    public function testCreateConnection(): void
    {
        $dtoMock = $this->createMock(CreateConnection::class);

        $messageBusMock = $this->createMock(MessageBusInterface::class);
        $messageBusMock->expects($this->once())
            ->method('dispatchAndGetResult')
            ->with($dtoMock, [])
            ->willReturn(['some' => 'data']);

        $ctrl = new ApiV1Controller($messageBusMock);

        $this->assertEquals(
            new JsonResponse([
                'some' => 'data',
            ]),
            $ctrl->create($dtoMock),
        );
    }
}
