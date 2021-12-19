<?php

namespace App\Connections\Handler\Response;

use App\Common\API\Error\FieldValidationErrorList;
use App\Common\Handler\ResponseInterface;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Model\Entity\Connection;

class CreateConnectionResponse implements ResponseInterface
{
    protected CreateConnectionInterface $cmd;
    protected ResponseStatus $status;
    protected FieldValidationErrorList $errors;
    protected ?Connection $connection;

    public function __construct(CreateConnectionInterface $cmd, ResponseStatus $status, FieldValidationErrorList $errors, ?Connection $connection)
    {
        $this->cmd = $cmd;
        $this->status = $status;
        $this->errors = $errors;
        $this->connection = $connection;
    }

    public function getErrors(): FieldValidationErrorList
    {
        return $this->errors;
    }

    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function getCommand(): CreateConnectionInterface
    {
        return $this->cmd;
    }
}
