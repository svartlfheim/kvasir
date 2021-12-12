<?php

namespace App\Tests\Unit\Connections\Controller;

use PHPUnit\Framework\TestCase;
use App\Connections\DTO\V1\ListConnections;
use App\Connections\DTO\V1\CreateConnection;
use App\Connections\Controller\ApiV1Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiV1ControllerTest extends TestCase
{
    public function testListConnections(): void
    {
        $dtoMock = $this->createMock(ListConnections::class);
        $dtoMock->expects($this->once())
            ->method('getLimit')
            ->willReturn(30);

        $ctrl = new ApiV1Controller();

        $this->assertEquals(
            new JsonResponse([
                'limit' => 30,
            ]),
            $ctrl->index($dtoMock),
        );
    }

    public function testCreateConnection(): void
    {
        $dtoMock = $this->createMock(CreateConnection::class);
        $dtoMock->expects($this->once())
            ->method('getName')
            ->willReturn('my-conn-name');
        $dtoMock->expects($this->once())
            ->method('getEngine')
            ->willReturn('my-engine-name');

        $ctrl = new ApiV1Controller();

        $this->assertEquals(
            new JsonResponse([
                'chosen_name' => 'my-conn-name',
                'chosen_engine' => 'my-engine-name',
            ]),
            $ctrl->create($dtoMock),
        );
    }
}
