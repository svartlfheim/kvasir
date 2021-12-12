<?php

namespace App\Common\Bus;

use Symfony\Component\DependencyInjection\ServiceLocator;

class CommandBus
{
    protected ServiceLocator $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function dispatch($command)
    {
        $commandClass = get_class($command);

        if ($this->locator->has($commandClass)) {
            $handler = $this->locator->get($commandClass);

            return $handler->handle($command);
        }

        throw new \RuntimeException("No handler found for command: $commandClass");
    }
}
