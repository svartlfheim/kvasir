<?php

namespace App\Tests\Unit\Common\ArgumentResolver\Stubs;

use App\Common\DTO\FromRequest;
use Symfony\Component\HttpFoundation\Request;

class ImplementsFromRequest implements FromRequest
{
    public static function fromRequest(Request $request): mixed
    {
        return new self();
    }
}
