<?php

namespace App\Common\API\Error;

class Violation
{
    protected string $message;
    protected string $rule;

    public function __construct(string $message, string $rule)
    {
        $this->message = $message;
        $this->rule = $rule;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRule(): string
    {
        return $this->rule;
    }
}
