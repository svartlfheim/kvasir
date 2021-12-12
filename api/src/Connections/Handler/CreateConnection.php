<?php

namespace App\Connections\Handler;

use App\Connections\Command\CreateConnection as Command;

class CreateConnection
{
    public function handle(Command $cmd)
    {
        dump("handling command...");
        dump($cmd);
    }
}
