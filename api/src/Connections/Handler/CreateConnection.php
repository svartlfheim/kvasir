<?php

namespace App\Connections\Handler;

use App\Connections\Command\CreateConnectionInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateConnection implements MessageHandlerInterface
{
    public function __invoke(CreateConnectionInterface $cmd)
    {
        return 'blah';
    }
}
