<?php

namespace App\Tests\Unit\Connections\Command\V1;

use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Command\V1\CreateConnection;
use App\Tests\Unit\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CreateConnectionTest extends TestCase
{
    public function testImplementsVersionAgnosticInterface(): void
    {
        $requestMock = $this->createMock(Request::class);

        $cmd = CreateConnection::fromRequest($requestMock);

        $this->assertInstanceOf(CreateConnectionInterface::class, $cmd);
    }

    public function testVersionIs1(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['name'], ['engine'])
            ->willReturnOnConsecutiveCalls('my-conn-name', 'my-engine-name');

        $cmd = CreateConnection::fromRequest($requestMock);

        $this->assertEquals(1, $cmd->version());
    }

    public function testAllValuesAreRetrievedFromRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['name'], ['engine'])
            ->willReturnOnConsecutiveCalls('my-conn-name', 'my-engine-name');

        $cmd = CreateConnection::fromRequest($requestMock);

        $this->assertEquals('my-conn-name', $cmd->getName());
        $this->assertEquals('my-engine-name', $cmd->getEngine());
    }

    public function testDefaults(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['name'], ['engine'])
            ->willReturnOnConsecutiveCalls(null, null);

        $cmd = CreateConnection::fromRequest($requestMock);

        $this->assertEquals('', $cmd->getName());
        $this->assertEquals('', $cmd->getEngine());
    }
}
