<?php

namespace App\Tests\Unit\Common\DI;

use App\Common\DI\RequiresMessageBus;
use App\Common\MessageBusInterface;
use App\Tests\Unit\TestCase;
use ReflectionObject;

class RequiresMessageBusTest extends TestCase
{
    public function testItSetsMessageBusInternally(): void
    {
        $bus = $this->createMock(MessageBusInterface::class);

        $testClass = new class () {
            use RequiresMessageBus;
        };

        $test = new $testClass();

        $test->withMessageBus($bus);

        $reflection = new ReflectionObject($test);
        $prop = $reflection->getProperty('bus');
        $prop->setAccessible(true);

        $this->assertSame($bus, $prop->getValue($test));
    }
}
