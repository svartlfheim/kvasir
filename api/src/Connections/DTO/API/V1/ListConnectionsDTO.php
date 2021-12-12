<?php

namespace App\Connections\DTO\API\V1;

use App\Common\DTO\FromRequest;
use Symfony\Component\HttpFoundation\Request;

class ListConnectionsDTO implements FromRequest
{
    protected $limit;

    protected function __construct($limit)
    {
        $this->limit = $limit;
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self($request->get('limit'));
    }

    public function getLimit(): int
    {
        return $this->limit ?? 20;
    }
}
