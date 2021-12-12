<?php

namespace App\Connections\Handler\Response;

use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Connections\Model\ConnectionList;
use App\Common\Handler\ResponseInterface;
use App\Connections\Command\ListConnectionsInterface;

class ListConnectionsResponse implements ResponseInterface
{
    protected ListConnectionsInterface $cmd;
    protected ResponseStatus $status;
    protected ConnectionList $connections;
    protected PaginationData $pagination;

    public function __construct(ListConnectionsInterface $cmd, ResponseStatus $status, ConnectionList $connections, PaginationData $pagination)
    {
        $this->cmd = $cmd;
        $this->status = $status;
        $this->connections = $connections;
        $this->pagination = $pagination;
    }

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function getPagination(): PaginationData
    {
        return $this->pagination;
    }

    public function getConnections(): ConnectionList
    {
        return $this->connections;
    }

    public function getCommand(): ListConnectionsInterface
    {
        return $this->cmd;
    }
}
