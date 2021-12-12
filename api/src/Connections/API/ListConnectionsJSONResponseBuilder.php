<?php

namespace App\Connections\API;

use App\Common\API\Metadata;
use App\Common\API\ArrayData;
use App\Common\API\JSONSerializableInterface;
use App\Common\API\BuildsJSONResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\Handler\Response\ListConnectionsResponse;

class ListConnectionsJSONResponseBuilder
{
    use BuildsJSONResponse;
    use SerializesConnections;

    public function fromCommandResponse(ListConnectionsResponse $resp): JsonResponse
    {
        $meta = (new Metadata())->withPagination($resp->getPagination());

        return $this->jsonResponse(
            $meta,
            $this->buildResponseData($resp),
            $resp->getStatus()
        );
    }

    protected function buildResponseData(ListConnectionsResponse $resp): JSONSerializableInterface
    {
        $data = [];

        foreach ($resp->getConnections() as $conn) {
            $version = $resp->getCommand()->version();
            $data[] = $this->serializeConnectionForVersion($version, $conn);
        }

        return new ArrayData($data);
    }
}
