<?php

namespace App\Common\API;

use App\Common\Handler\ResponseStatus;
use Symfony\Component\HttpFoundation\JsonResponse;

trait BuildsJSONResponse
{
    protected static $httpMapping = [
        ResponseStatus::STATUS_OK => 200,
        ResponseStatus::STATUS_CREATED => 201,
        ResponseStatus::STATUS_VALIDATION_ERROR => 422,
        ResponseStatus::STATUS_ERROR => 500,
    ];

    protected function httpStatusCode(ResponseStatus $status): int
    {
        $statusName = (string) $status;
        if (! array_key_exists($statusName, self::$httpMapping)) {
            throw new \RuntimeException("Could not map status '$status' to http code.");
        }

        return self::$httpMapping[$statusName];
    }

    /**
     * We'll need to deal with headers at some point...
     */
    protected function jsonResponse(JSONSerializableInterface $metadata, ?JSONSerializableInterface $data, JSONSerializableInterface $errors, ResponseStatus $status): JsonResponse
    {
        return new JsonResponse(
            [
                'meta' => $metadata->toJSON(),
                'data' => $data !== null ? $data->toJSON() : null,
                'errors' => $errors->toJSON(),
            ],
            $this->httpStatusCode($status),
        );
    }
}
