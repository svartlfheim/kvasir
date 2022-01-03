<?php

namespace App\Tests\Unit\Common\ArgumentResolver\Stubs;

use App\Common\Command\FromRequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ImplementsFromRequest implements FromRequestInterface
{
    public static function fromRequest(Request $request): mixed
    {
        return new self();
    }
}
