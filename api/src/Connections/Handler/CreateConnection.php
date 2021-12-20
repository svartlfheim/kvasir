<?php

namespace App\Connections\Handler;

use App\Common\Command\CommandValidatorInterface;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Handler\Response\Factory;
use App\Connections\Model\Entity\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateConnection implements MessageHandlerInterface
{
    protected CommandValidatorInterface $commandValidator;
    protected Factory $responseFactory;

    public function __construct(Factory $responseFactory, CommandValidatorInterface $commandValidator)
    {
        $this->responseFactory = $responseFactory;
        $this->commandValidator = $commandValidator;
    }

    public function __invoke(CreateConnectionInterface $cmd): CreateConnectionResponse
    {
        $fieldErrors = $this->commandValidator->validate($cmd);

        /** @var CreateConnectionResponse */
        $resp = $this->responseFactory->make(CreateConnectionResponse::class)
            ->setCommand($cmd)
            ->setErrors($fieldErrors);

        if (! $fieldErrors->isEmpty()) {
            return $resp->setStatus(ResponseStatus::newValidationError())
                ->setConnection(null);
        }

        return $resp->setStatus(ResponseStatus::newCreated())
            ->setConnection(Connection::create('faked-conn', Connection::ENGINE_MYSQL));
    }
}
