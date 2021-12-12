<?php

namespace App\Connections\DTO\V1;

use App\Common\DTO\FromRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\DTO\ListConnectionsInterface;

class ListConnections implements FromRequest, ListConnectionsInterface
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
