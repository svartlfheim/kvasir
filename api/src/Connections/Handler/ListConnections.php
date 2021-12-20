<?php

namespace App\Connections\Handler;

use App\Common\API\PaginationData;
use App\Common\DI\RequiresValidator;
use App\Common\Handler\ResponseStatus;
use App\Common\Handler\ValidatesCommand;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\Factory;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListConnections implements MessageHandlerInterface
{
    use RequiresValidator;
    use ValidatesCommand;

    protected Factory $responseFactory;

    public function __construct(Factory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(ListConnectionsInterface $cmd): ListConnectionsResponse
    {
        $fieldErrors = $this->validateCommand($cmd);

        /** @var ListConnectionsResponse */
        $resp = $this->responseFactory->make(ListConnectionsResponse::class)
            ->setCommand($cmd)
            ->setErrors($fieldErrors);

        if (! $fieldErrors->isEmpty()) {
            return $resp->setConnections(ConnectionList::empty())
                ->setPagination(new PaginationData())
                ->setStatus(ResponseStatus::newValidationError());
        }

        $pagination = (new PaginationData())->withOrderBy(
            $cmd->getOrderField(),
            $cmd->getOrderDirection(),
        );

        return $resp->setConnections(ConnectionList::empty())
            ->setPagination($pagination)
            ->setStatus(ResponseStatus::newOK());
    }
}
