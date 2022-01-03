<?php

namespace App\Common\Command;

use Symfony\Component\HttpFoundation\Request;

/* Used by the CommandResolver to determine whether this can automatically resolved. */
interface FromRequestInterface
{
    public static function fromRequest(Request $request): mixed;
}
