<?php

namespace App\Tests\Unit\Connections\DTO\V1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\DTO\V1\CreateConnection;

class CreateConnectionTest extends TestCase
{
    public function testAllValuesAreRetrievedFromRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['name'], ['engine'])
            ->willReturnOnConsecutiveCalls('my-conn-name', 'my-engine-name');

        $dto = CreateConnection::fromRequest($requestMock);

        $this->assertEquals('my-conn-name', $dto->getName());
        $this->assertEquals('my-engine-name', $dto->getEngine());
    }

    public function testLimitIsDefaultedTo20(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['name'], ['engine'])
            ->willReturnOnConsecutiveCalls(null, null);

        $dto = CreateConnection::fromRequest($requestMock);

        $this->assertEquals('', $dto->getName());
        $this->assertEquals('', $dto->getEngine());
    }
}
