<?php

namespace App\Common\API;

use App\Common\API\Error\FieldValidationError;
use App\Common\API\Error\FieldValidationErrorList;
use App\Common\Attributes\HTTPField;
use App\Common\Handler\ResponseStatus;
use ReflectionObject;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

class HTTPResponseBuilder
{
    protected ?JSONSerializableInterface $metadata = null;
    protected ?JSONSerializableInterface $data = null;
    protected ?FieldValidationErrorList $errors = null;
    protected ?ResponseStatus $responseStatus = null;

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
            if (! $cmdReflection->hasProperty($key)) {
                // This would mean it was a rule on a class level.
                // We'll always map these to the reserved '_composites' field name.
                $mapped->add(
                    FieldValidationError::new('_composites', $error->getViolations())
                );

                continue;
            }

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

    public function withMeta(JSONSerializableInterface $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function withData(?JSONSerializableInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function withErrors(FieldValidationErrorList $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function withHTTPMappedErrors(FieldValidationErrorList $errors, object $cmd): self
    {
        $this->errors = $this->mapValidationErrorsToHTTPField($cmd, $errors);

        return $this;
    }

    public function withStatus(ResponseStatus $status): self
    {
        $this->responseStatus = $status;

        return $this;
    }

    protected function guardStatus(): void
    {
        if ($this->responseStatus === null) {
            throw new RuntimeException("Response status cannot be null in an HTTP response.");
        }
    }

    /**
     * We'll need to deal with headers at some point...
     */
    public function json(): JsonResponse
    {
        $this->guardStatus();

        return new JsonResponse(
            [
                'meta' => $this->metadata !== null ? $this->metadata->toJSON() : [],
                'data' => $this->data !== null ? $this->data->toJSON() : null,
                'errors' => $this->errors !== null ? $this->errors->toJSON() : [],
            ],
            $this->httpStatusCode($this->responseStatus),
        );
    }
}
