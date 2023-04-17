<?php

namespace App\Common;

use Symfony\Component\Messenger\MessageBusInterface as Base;

interface MessageBusInterface extends Base
{
    /**
     * This allows us to more easily work with the message bus for our purposes.
     *
     * Testing with the base message bus interface can be tricky as the return types are 'final'.
     * This meant we couldn't mock the functionality very well, of retrieving the result in a controller.
     */
    public function dispatchAndGetResult(object $message, array $stamps = []): mixed;
}
