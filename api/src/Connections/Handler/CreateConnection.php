<?php

namespace App\Connections\Handler;

use App\Common\Command\CommandValidatorInterface;
use App\Common\Generator\Uuid;
use App\Common\Handler\ResponseStatus;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Handler\Response\CreateConnectionResponse;
use App\Connections\Handler\Response\Factory;
use App\Connections\Model\Entity\Connection;
use App\Connections\Repository\ConnectionsInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateConnection implements MessageHandlerInterface
{
    protected CommandValidatorInterface $commandValidator;
    protected Factory $responseFactory;
    protected ConnectionsInterface $repo;
    protected Uuid $uuidGenerator;

    public function __construct(
        Factory $responseFactory,
        CommandValidatorInterface $commandValidator,
        ConnectionsInterface $repo,
        Uuid $uuidGenerator
    ) {
        $this->responseFactory = $responseFactory;
        $this->commandValidator = $commandValidator;
        $this->repo = $repo;
        $this->uuidGenerator = $uuidGenerator;
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

        $conn = $this->repo->save(
            Connection::create(
                $this->uuidGenerator->generate(),
                $cmd->getName(),
                $cmd->getEngine()
            )
        );

        return $resp->setStatus(ResponseStatus::newCreated())
            ->setConnection($conn);
    }
}
