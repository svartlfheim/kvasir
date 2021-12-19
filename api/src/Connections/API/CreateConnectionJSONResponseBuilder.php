<?php

namespace App\Connections\API;

use App\Common\API\ArrayData;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\JSONSerializableInterface;
use App\Common\API\Metadata;
use App\Connections\Handler\Response\CreateConnectionResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateConnectionJSONResponseBuilder
{
    protected ConnectionSerializer $serializer;
    protected HTTPResponseBuilder $httpResponseBuilder;

    public function __construct(ConnectionSerializer $serializer, HTTPResponseBuilder $httpResponseBuilder)
    {
        $this->serializer = $serializer;
        $this->httpResponseBuilder = $httpResponseBuilder;
    }

    public function fromCommandResponse(CreateConnectionResponse $resp): JsonResponse
    {
        $meta = new Metadata();

        return $this->httpResponseBuilder->json(
            $meta,
            $this->buildResponseData($resp),
            $this->httpResponseBuilder->mapValidationErrorsToHTTPField($resp->getCommand(), $resp->getErrors()),
            $resp->getStatus()
        );
    }

    protected function buildResponseData(CreateConnectionResponse $resp): ?JSONSerializableInterface
    {
        if (! $resp->getErrors()->isEmpty()) {
            return null;
        }

        $this->serializer->setVersion($resp->getCommand()->version());

        return new ArrayData(
            $this->serializer->serialize(
                $resp->getConnection()
            )
        );
    }
}
