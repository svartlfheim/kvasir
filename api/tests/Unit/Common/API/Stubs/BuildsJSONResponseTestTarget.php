<?php

namespace App\Tests\Unit\Common\API\Stubs;

use App\Common\API\BuildsJSONResponse;
use App\Common\Handler\ResponseStatus;
use App\Common\API\JSONSerializableInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BuildsJSONResponseTestTarget
{
    use BuildsJSONResponse;

    public function getHTTPStatusCode(ResponseStatus $status): int
    {
        return $this->httpStatusCode($status);
    }

    public function buildResponse(JSONSerializableInterface $metadata, JSONSerializableInterface $data, ResponseStatus $status): JsonResponse
    {
        return $this->jsonResponse($metadata, $data, $status);
    }
}
