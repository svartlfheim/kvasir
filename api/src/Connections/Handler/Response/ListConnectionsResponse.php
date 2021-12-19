<?php

namespace App\Connections\Handler\Response;

use App\Common\API\PaginationData;
use App\Common\Handler\ResponseStatus;
use App\Common\Handler\ResponseInterface;
use App\Connections\Model\ConnectionList;
use App\Common\API\Error\FieldValidationErrorList;
use App\Connections\Command\ListConnectionsInterface;

class ListConnectionsResponse implements ResponseInterface
{
    protected ListConnectionsInterface $cmd;
    protected ResponseStatus $status;
    protected FieldValidationErrorList $errors;
    protected ConnectionList $connections;
    protected PaginationData $pagination;

    public function __construct(ListConnectionsInterface $cmd, ResponseStatus $status, FieldValidationErrorList $errors, ConnectionList $connections, PaginationData $pagination)
    {
        $this->cmd = $cmd;
        $this->status = $status;
        $this->errors = $errors;
        $this->connections = $connections;
        $this->pagination = $pagination;
    }

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function getErrors(): FieldValidationErrorList
    {
        return $this->errors;
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
