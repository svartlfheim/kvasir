<?php

namespace App\Connections\Handler;

use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\ConnectionList;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\ListConnectionsResponse;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListConnections implements MessageHandlerInterface
{
    public function __invoke(ListConnectionsInterface $cmd): ListConnectionsResponse
    {
        $pagination = (new PaginationData())
            ->withOrderBy('name', 'ASC');

        return new ListConnectionsResponse(
            $cmd,
            ResponseStatus::newOK(),
            ConnectionList::empty(),
            $pagination,
        );
    }
}
