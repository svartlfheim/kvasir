<?php

namespace App\Common\Bus;

interface ResponseInterface
{
    public function getStatus(): string;
}
