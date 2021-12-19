<?php

namespace App\Common\DI;

use Symfony\Contracts\Service\Attribute\Required;
use App\Common\MessageBusInterface;

/**
 * Used to automatically inject the message bus to any service that needs it.
 */
trait RequiresMessageBus
{
    protected ?MessageBusInterface $bus = null;

    #[Required]
    public function withMessageBus(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }
}
