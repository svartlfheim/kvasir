<?php

namespace App\Tests\Unit\Connections\DTO\V1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\DTO\V1\ListConnections;

class ListConnectionsTest extends TestCase
{
    public function testLimitIsRetrievedFromRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('limit')
            ->willReturn(30);

        $dto = ListConnections::fromRequest($requestMock);

        $this->assertEquals(30, $dto->getLimit());
    }

    public function testLimitIsDefaultedTo20(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('get')
            ->with('limit')
            ->willReturn(null);

        $dto = ListConnections::fromRequest($requestMock);

        $this->assertEquals(20, $dto->getLimit());
    }
}
