<?php

namespace App\Tests\Unit\Connections\Command\V1;

use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\Command\V1\ListConnections;
use App\Connections\Command\ListConnectionsInterface;

class ListConnectionsTest extends TestCase
{
    public function testImplementsVersionAgnosticInterface(): void
    {
        $requestMock = $this->createMock(Request::class);

        $dto = ListConnections::fromRequest($requestMock);

        $this->assertInstanceOf(ListConnectionsInterface::class, $dto);
    }

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
