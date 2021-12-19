<?php

namespace App\Connections\API;

use App\Common\API\ArrayData;
use App\Common\API\HTTPResponseBuilder;
use App\Common\API\JSONSerializableInterface;
use App\Common\API\Metadata;
use App\Connections\Handler\Response\ListConnectionsResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ListConnectionsJSONResponseBuilder
{
    protected ConnectionSerializer $serializer;
    protected HTTPResponseBuilder $httpResponseBuilder;

    public function __construct(ConnectionSerializer $serializer, HTTPResponseBuilder $httpResponseBuilder)
    {
        $this->serializer = $serializer;
        $this->httpResponseBuilder = $httpResponseBuilder;
    }

    public function fromCommandResponse(ListConnectionsResponse $resp): JsonResponse
    {
        $meta = (new Metadata())->withPagination($resp->getPagination());

        return $this->httpResponseBuilder->withMeta($meta)
            ->withData($this->buildResponseData($resp))
            ->withHTTPMappedErrors($resp->getErrors(), $resp->getCommand())
            ->withStatus($resp->getStatus())
            ->json();
    }

    protected function buildResponseData(ListConnectionsResponse $resp): JSONSerializableInterface
    {
        $connList = $resp->getConnections();

        if ($connList->isEmpty()) {
            return new ArrayData([]);
        }

        $data = [];

        $this->serializer->setVersion($resp->getCommand()->version());
        foreach ($connList as $conn) {
            $data[] = $this->serializer->serialize($conn);
        }

        return new ArrayData($data);
    }
}
