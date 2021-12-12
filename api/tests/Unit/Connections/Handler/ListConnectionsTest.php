<?php

namespace App\Tests\Unit\Connections\Handler;

use PHPUnit\Framework\TestCase;
use App\Connections\Handler\ListConnections;
use App\Connections\Command\ListConnectionsInterface;

class ListConnectionsTest extends TestCase
{
    public function testItIsInvokable(): void
    {
        $cmd = $this->createMock(ListConnectionsInterface::class);
        $cmd->expects($this->once())
            ->method('getLimit')
            ->willReturn(10);
        $handler = new ListConnections();

        $this->assertEquals(['limit' => 10], $handler($cmd));
    }
}
