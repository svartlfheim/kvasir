<?php

namespace App\Connections\Handler;

use App\Common\DI\RequiresValidator;
use App\Common\Handler\ResponseStatus;
use App\Common\Handler\ValidatesCommand;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateConnection implements MessageHandlerInterface
{
    use RequiresValidator;
    use ValidatesCommand;

    public function __invoke(CreateConnectionInterface $cmd)
    {
        $fieldErrors = $this->validateCommand($cmd);

        if (! $fieldErrors->isEmpty()) {
            return new CreateConnectionResponse(
                $cmd,
                ResponseStatus::newValidationError(),
                $fieldErrors,
                null,
            );
        }

        // Need to do the actual adding of a connection, for now it just returns null
        return new CreateConnectionResponse(
            $cmd,
            ResponseStatus::newCreated(),
            $fieldErrors,
            null,
        );
    }
}
