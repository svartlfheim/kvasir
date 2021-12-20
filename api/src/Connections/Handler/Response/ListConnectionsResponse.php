<?php

namespace App\Connections\Handler\Response;

use App\Common\API\ArrayData;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\Metadata;
use App\Common\API\PaginationData;
use App\Common\Handler\ResponseInterface;
use App\Common\Handler\ResponseStatus;
use App\Connections\API\ConnectionSerializer;
use App\Connections\Command\ListConnectionsInterface;
use App\Connections\Model\ConnectionList;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListConnectionsResponse implements ResponseInterface, ConnectionResponseInterface
{
    protected ConnectionSerializer $serializer;
    protected HTTPResponseBuilder $httpResponseBuilder;
    protected ?ListConnectionsInterface $cmd = null;
    protected ?ResponseStatus $status = null;
    protected ?FieldValidationErrorList $errors = null;
    protected ?ConnectionList $connections = null;
    protected ?PaginationData $pagination = null;

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

    public function setConnections(ConnectionList $connections): self
    {
        $this->connections = $connections;

        return $this;
    }

    public function getConnections(): ?ConnectionList
    {
        return $this->connections;
    }

    public function setCommand(ListConnectionsInterface $cmd): self
    {
        $this->cmd = $cmd;

        return $this;
    }

    public function getCommand(): ?ListConnectionsInterface
    {
        return $this->cmd;
    }

    public function setPagination(PaginationData $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    public function getPagination(): ?PaginationData
    {
        return $this->pagination;
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

        if ($this->getPagination() === null) {
            $missingProps[] = 'pagination';
        }

        if (! empty($missingProps)) {
            throw new RuntimeException('The following required props were not set for json response: ' . implode(', ', $missingProps));
        }
    }

    public function json(): JsonResponse
    {
        $this->guardRequiredPropsForJson();

        $respData = new ArrayData([]);
        $connList = $this->getConnections() ?? ConnectionList::empty();

        if (! $connList->isEmpty()) {
            $data = [];

            $this->serializer->setVersion($this->getCommand()->version());
            foreach ($connList as $conn) {
                $data[] = $this->serializer->serialize($conn);
            }

            $respData =  new ArrayData($data);
        }

        $meta = (new Metadata())->withPagination($this->getPagination());

        return $this->httpResponseBuilder->withMeta($meta)
            ->withData($respData)
            ->withHTTPMappedErrors($this->getErrors(), $this->getCommand())
            ->withStatus($this->getStatus())
            ->json();
    }
}
