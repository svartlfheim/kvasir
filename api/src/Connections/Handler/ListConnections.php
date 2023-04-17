<?php

namespace App\Connections\Handler;

use App\Common\API\PaginationData;
use App\Common\Command\CommandValidatorInterface;
use App\Common\Database\ColumnSortOrder;
use App\Common\Database\ListOptions;
use App\Common\Database\Pagination;
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

        $orderField = $cmd->getOrderField();
        $orderDirection = $cmd->getOrderDirection();
        $page = $cmd->getPage();
        $pageSize = $cmd->getPageSize();

        $listOpts = (new ListOptions())
            ->addSortOrder(ColumnSortOrder::new($orderField, $orderDirection))
            ->setPagination(new Pagination($page, $pageSize));

        $conns = $this->repo->all($listOpts);
        $pagination = (new PaginationData())
            ->withPage($page)
            ->withPageSize($pageSize)
            ->withOrderBy(
                $orderField,
                $orderDirection,
            );

        return $resp->setConnections($conns)
            ->setPagination($pagination)
            ->setStatus(ResponseStatus::newOK());
    }
}
