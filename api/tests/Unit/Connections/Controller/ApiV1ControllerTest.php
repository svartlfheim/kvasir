<?php

namespace App\Tests\Unit\Connections\Controller;

use PHPUnit\Framework\TestCase;
use App\Connections\Controller\ApiV1Controller;
use App\Connections\DTO\API\V1\ListConnectionsDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiV1ControllerTest extends TestCase
{
    public function testLimitIsReturnedFromDTO(): void
    {
        $dtoMock = $this->createMock(ListConnectionsDTO::class);
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
}
