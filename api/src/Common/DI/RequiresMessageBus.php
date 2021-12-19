<?php

namespace App\Common\DI;

use App\Common\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Used to automatically inject the message bus to any service that needs it.
 */
trait RequiresMessageBus
{
    protected ?MessageBusInterface $bus = null;

    #[Required]
    public function withMessageBus(MessageBusInterface $bus): void
    {
        $this->bus = $bus;
    }
}
