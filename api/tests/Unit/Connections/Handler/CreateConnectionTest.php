<?php

namespace App\Tests\Unit\Connections\Handler;

use App\Tests\Unit\TestCase;
use App\Connections\Handler\CreateConnection;
use App\Connections\Command\CreateConnectionInterface;

class CreateConnectionTest extends TestCase
{
    public function testItIsInvokable(): void
    {
        $cmd = $this->createMock(CreateConnectionInterface::class);
        $handler = new CreateConnection();

        $this->assertEquals('blah', $handler($cmd));
    }
}
