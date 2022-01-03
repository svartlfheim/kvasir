<?php

namespace App\Common\Attributes;

use Attribute;

/**
 * @see https://www.php.net/manual/en/language.attributes.classes.php
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HTTPField
{
    protected string $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }

    public function getReference(): string
    {
        return $this->reference;
    }
}
