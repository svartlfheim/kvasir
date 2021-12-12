<?php

namespace App\Connections\Handler;

use App\Connections\Command\ListConnections as Command;

class ListConnections
{
    public function handle(Command $cmd)
    {
        dump("handling command...");
        dump($cmd);
    }
}
