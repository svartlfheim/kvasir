<?php

namespace App\Common\Handler;

interface ResponseInterface
{
    public function getStatus(): ResponseStatus;
}
