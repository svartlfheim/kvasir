<?php

namespace App\Connections\Handler\Response;

use App\Common\API\ArrayData;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\Metadata;
use App\Common\Handler\ResponseInterface;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\Command\CreateConnectionInterface;
use App\Connections\Model\Entity\Connection;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateConnectionResponse implements ResponseInterface, ConnectionResponseInterface
{
    protected ConnectionSerializer $serializer;
    protected HTTPResponseBuilder $httpResponseBuilder;
    protected ?CreateConnectionInterface $cmd = null;
    protected ?ResponseStatus $status = null;
    protected ?FieldValidationErrorList $errors = null;
    protected ?Connection $connection = null;

    public function __construct(ConnectionSerializer $serializer, HTTPResponseBuilder $httpResponseBuilder)
    {
        $this->serializer = $serializer;
        $this->httpResponseBuilder = $httpResponseBuilder;
    }

    public function setErrors(FieldValidationErrorList $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): ?FieldValidationErrorList
    {
        return $this->errors;
    }

    public function setStatus(ResponseStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?ResponseStatus
    {
        return $this->status;
    }

    public function setConnection(?Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function setCommand(CreateConnectionInterface $cmd): self
    {
        $this->cmd = $cmd;

        return $this;
    }

    public function getCommand(): ?CreateConnectionInterface
    {
        return $this->cmd;
    }

    protected function guardRequiredPropsForJson(): void
    {
        $missingProps = [];
        if ($this->getErrors() === null) {
            $missingProps[] = 'errors';
        }

        if ($this->getCommand() === null) {
            $missingProps[] = 'cmd';
        }

        if ($this->getStatus() === null) {
            $missingProps[] = 'status';
        }

        if (! empty($missingProps)) {
            throw new RuntimeException('The following required props were not set for json response: ' . implode(', ', $missingProps));
        }
    }

    public function json(): JsonResponse
    {
        $this->guardRequiredPropsForJson();

        $responseData = null;
        if ($this->getErrors()->isEmpty()) {
            $this->serializer->setVersion($this->getCommand()->version());

            $responseData = new ArrayData(
                $this->serializer->serialize(
                    $this->getConnection(),
                )
            );
        }

        $meta = new Metadata();

        return $this->httpResponseBuilder->withMeta($meta)
            ->withData($responseData)
            ->withHTTPMappedErrors($this->getErrors(), $this->getCommand())
            ->withStatus($this->getStatus())
            ->json();
    }
}
