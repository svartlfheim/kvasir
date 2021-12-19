<?php

namespace App\Tests\Unit\Common;

use App\Common\MessageBus;
use App\Common\MessageBusInterface;
use App\Tests\Unit\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface as BaseMessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessageBusTest extends TestCase
{
    /*
    Wouldn't normally test something like this...
    But we'll use this interface for dependency injection, rather than concrete class
    So we wanna make sure it implements the interface to ensure that DI will work later
    A proper integration tests should be written at some point anyway, but belt and braces
    */
    public function testItImplementsTheMessageBusInterface(): void
    {
        $base = $this->createMock(BaseMessageBusInterface::class);

        $bus = new MessageBus($base);

        $this->assertInstanceOf(MessageBusInterface::class, $bus);
    }

    /**
     * We can't mock the stamps or envelope as they're final
     * So we need to create a 'real' version of these in the tests below
     */
    public function testItSendsDispatchToBaseBus(): void
    {
        $msgClass = new class () {};
        $msg = new $msgClass();
        $stamps = [
            new HandledStamp(1, 'fakehandler'),
        ];

        $base = $this->createMock(BaseMessageBusInterface::class);
        $base->expects($this->once())
            ->method('dispatch')
            ->with($msg, $stamps)
            ->willReturn(new Envelope($msg, $stamps));

        $bus = new MessageBus($base);

        $this->assertInstanceOf(Envelope::class, $bus->dispatch($msg, $stamps));
    }

    public function testItDispatchesAndReturnsResult(): void
    {
        $msgClass = new class () {};
        $msg = new $msgClass();
        $stamps = [
            new HandledStamp('myresult', 'fakehandler'),
        ];

        $base = $this->createMock(BaseMessageBusInterface::class);
        $base->expects($this->once())
            ->method('dispatch')
            ->with($msg, $stamps)
            ->willReturn(new Envelope($msg, $stamps));

        $bus = new MessageBus($base);

        $this->assertEquals('myresult', $bus->dispatchAndGetResult($msg, $stamps));
    }
}
