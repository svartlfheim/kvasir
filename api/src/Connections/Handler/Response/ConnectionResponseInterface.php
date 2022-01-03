<?php

namespace App\Connections\Handler\Response;

use App\Common\API\HTTPResponseBuilder;
use App\Connections\API\ConnectionSerializer;

interface ConnectionResponseInterface
{
    public function __construct(ConnectionSerializer $serializer, HTTPResponseBuilder $httpResponseBuilder);
}
