<?php

namespace App\Connections\Handler\Response;

use App\Common\API\HTTPResponseBuilder;
use App\Connections\API\ConnectionSerializer;
use InvalidArgumentException;

class Factory
{
    protected ConnectionSerializer $serializer;

    public function __construct(ConnectionSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function make(string $class)
    {
        $implemented = class_implements($class);

        if ($implemented === false || ! in_array(ConnectionResponseInterface::class, $implemented)) {
            throw new InvalidArgumentException("Class '$class' must implement " . ConnectionResponseInterface::class);
        }

        return new $class($this->serializer, new HTTPResponseBuilder());
    }
}
