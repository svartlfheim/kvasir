<?php

namespace App\Tests\Unit\Connections\DTO\API\V1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\DTO\API\V1\ListConnectionsDTO;

class ListConnectionsDTOTest extends TestCase
{
    public function testLimitIsRetrievedFromRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('limit')
            ->willReturn(30);

        $dto = ListConnectionsDTO::fromRequest($requestMock);

        $this->assertEquals(30, $dto->getLimit());
    }

    public function testLimitIsDefaultedTo20(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('limit')
            ->willReturn(null);

        $dto = ListConnectionsDTO::fromRequest($requestMock);

        $this->assertEquals(20, $dto->getLimit());
    }
}
