<?php

namespace App\Common\DTO;

use Symfony\Component\HttpFoundation\Request;

/* Used by the DTOResolver to determine whether this can automatically resolved. */
interface FromRequest
{
    public static function fromRequest(Request $request): mixed;
}
