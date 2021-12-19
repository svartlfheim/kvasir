<?php

namespace App\Common\API;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\Attributes\HTTPField;
use App\Common\Handler\ResponseStatus;
use ReflectionObject;
use Symfony\Component\HttpFoundation\JsonResponse;

class HTTPResponseBuilder
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

    public function mapValidationErrorsToHTTPField(object $cmd, FieldValidationErrorList $errors): FieldValidationErrorList
    {
        $cmdReflection = new ReflectionObject($cmd);
        $mapped = FieldValidationErrorList::empty();

        foreach ($errors as $error) {
            $key = $error->getFieldName();
            $prop = $cmdReflection->getProperty($key);

            /** @var $attributes []ReflectionAttribute */
            $attributes = $prop->getAttributes(HTTPField::class);

            if (count($attributes) > 1) {
                throw new \RuntimeException("Attribute: '" . HTTPField::class . "' declared multiple times on " . get_class($cmd) . "::" . $key);
            } elseif (count($attributes) == 1) {
                $key = $attributes[0]->newInstance()->getReference();
            }

            // If there are no attributes we'll use the property name
            $mapped->add(
                FieldValidationError::new($key, $error->getViolations())
            );
        }

        return $mapped;
    }

    /**
     * We'll need to deal with headers at some point...
     */
    public function json(JSONSerializableInterface $metadata, ?JSONSerializableInterface $data, FieldValidationErrorList $errors, ResponseStatus $status): JsonResponse
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
