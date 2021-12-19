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

        $cmd = ListConnections::fromRequest($requestMock);

        $this->assertInstanceOf(ListConnectionsInterface::class, $cmd);
    }

    public function tstVersionIs1(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(['page_size'], ['page'], ['order_field'], ['order_direction'])
            ->willReturnOnConsecutiveCalls(10, 'currpage', 'somefield', 'desc');

        $cmd = ListConnections::fromRequest($requestMock);

        $this->assertEquals(1, $cmd->version());
    }

    public function testPropsArePulledFromRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(['page_size'], ['page'], ['order_field'], ['order_direction'])
            ->willReturnOnConsecutiveCalls(10, 'currpage', 'somefield', 'desc');

        $cmd = ListConnections::fromRequest($requestMock);

        $this->assertEquals(10, $cmd->getPageSize());
        $this->assertEquals('currpage', $cmd->getPage());
        $this->assertEquals('somefield', $cmd->getOrderField());
        $this->assertEquals('desc', $cmd->getOrderDirection());
    }

    public function testDefaults(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(4))
            ->method('get')
            ->withConsecutive(['page_size'], ['page'], ['order_field'], ['order_direction'])
            ->willReturnOnConsecutiveCalls(null, null, null, null);

        $cmd = ListConnections::fromRequest($requestMock);

        $this->assertEquals(20, $cmd->getPageSize());
        $this->assertEquals('', $cmd->getPage());
        $this->assertEquals('name', $cmd->getOrderField());
        $this->assertEquals('asc', $cmd->getOrderDirection());
    }
}
