<?php

namespace App\Connections\Handler;

use App\Common\API\PaginationData;
use App\Common\Command\CommandValidatorInterface;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Handler\Response\Factory;
use App\Connections\Handler\Response\ListConnectionsResponse;
use App\Connections\Model\ConnectionList;
use App\Connections\Repository\ConnectionsInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ListConnections implements MessageHandlerInterface
{
    protected CommandValidatorInterface $commandValidator;
    protected Factory $responseFactory;
    protected ConnectionsInterface $repo;

    public function __construct(Factory $responseFactory, CommandValidatorInterface $commandValidator, ConnectionsInterface $repo)
    {
        $this->responseFactory = $responseFactory;
        $this->commandValidator = $commandValidator;
        $this->repo = $repo;
    }


    public function __invoke(ListConnectionsInterface $cmd): ListConnectionsResponse
    {
        $fieldErrors = $this->commandValidator->validate($cmd);

        /** @var ListConnectionsResponse */
        $resp = $this->responseFactory->make(ListConnectionsResponse::class)
            ->setCommand($cmd)
            ->setErrors($fieldErrors);

        if (! $fieldErrors->isEmpty()) {
            return $resp->setConnections(ConnectionList::empty())
                ->setPagination(new PaginationData())
                ->setStatus(ResponseStatus::newValidationError());
        }

        $conns = $this->repo->all();
        $pagination = (new PaginationData())->withOrderBy(
            $cmd->getOrderField(),
            $cmd->getOrderDirection(),
        );

        return $resp->setConnections($conns)
            ->setPagination($pagination)
            ->setStatus(ResponseStatus::newOK());
    }
}
