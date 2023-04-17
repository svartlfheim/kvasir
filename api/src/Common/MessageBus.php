<?php

namespace App\Common;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface as BaseMessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * So I wrapped the default message bus for convenience.
 * We're not really using async messages, we're using this to implement the comand pattern.
 * Basically i wanted a short hand for the controllers to dispatch a job and get the result in a one-liner.
 * This achieves that by adding an extra method to the standard interface.
 */
class MessageBus implements MessageBusInterface
{
    protected BaseMessageBusInterface $bus;

    public function __construct(BaseMessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @see MessageBusInterface::dispatch
     */
    public function dispatchAndGetResult(object $message, array $stamps = []): mixed
    {
        $res = $this->bus->dispatch($message, $stamps);

        /** @var HandledStamp */
        $stamp = $res->last(HandledStamp::class);

        return $stamp->getResult();
    }

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        return $this->bus->dispatch($message, $stamps);
    }
}
