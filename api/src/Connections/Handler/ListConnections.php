<?php

namespace App\Connections\Handler;

use App\Common\API\PaginationData;
use App\Common\DI\RequiresValidator;
use App\Common\Handler\ResponseStatus;
use App\Common\Handler\ValidatesCommand;
use App\Connections\Model\ConnectionList;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListConnections implements MessageHandlerInterface
{
    use RequiresValidator;
    use ValidatesCommand;

    public function __invoke(ListConnectionsInterface $cmd): ListConnectionsResponse
    {
        $pagination = (new PaginationData());

        $fieldErrors = $this->validateCommand($cmd);

        if (! $fieldErrors->isEmpty()) {
            return new ListConnectionsResponse(
                $cmd,
                ResponseStatus::newValidationError(),
                $fieldErrors,
                ConnectionList::empty(),
                $pagination,
            );
        }

        $pagination->withOrderBy(
            $cmd->getOrderField(),
            $cmd->getOrderDirection(),
        );

        return new ListConnectionsResponse(
            $cmd,
            ResponseStatus::newOK(),
            $fieldErrors,
            ConnectionList::empty(),
            $pagination,
        );
    }
}
