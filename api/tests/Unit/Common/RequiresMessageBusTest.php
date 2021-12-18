<?php

namespace App\Tests\Unit\Common;

use ReflectionObject;
use App\Tests\Unit\TestCase;
use App\Common\RequiresMessageBus;
use App\Common\MessageBusInterface;

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
