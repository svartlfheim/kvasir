<?php

namespace App\Connections\API;

use App\Common\API\Metadata;
use App\Common\API\ArrayData;
use App\Common\API\JSONSerializableInterface;
use App\Common\API\BuildsJSONResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Connections\Handler\Response\CreateConnectionResponse;

class CreateConnectionJSONResponseBuilder
{
    use BuildsJSONResponse;
    use SerializesConnections;

    public function fromCommandResponse(CreateConnectionResponse $resp): JsonResponse
    {
        $meta = new Metadata();

        return $this->jsonResponse(
            $meta,
            $this->buildResponseData($resp),
            $this->buildErrorData($resp),
            $resp->getStatus()
        );
    }

    protected function buildErrorData(CreateConnectionResponse $resp): JSONSerializableInterface
    {
        $errors = $resp->getErrors();

        if ($errors->isEmpty()) {
            return new ArrayData([]);
        }

        return new ArrayData($errors->toJSON());
    }

    protected function buildResponseData(CreateConnectionResponse $resp): ?JSONSerializableInterface
    {
        if (! $resp->getErrors()->isEmpty()) {
            return null;
        }

        return new ArrayData(
            $this->serializeConnectionForVersion(
                $resp->getCommand()->version(),
                $resp->getConnection()
            )
        );
    }
}
