<?php

namespace App\Connections\Command\V1;

use App\Common\Command\FromRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Connections\Command\ListConnectionsInterface;

class ListConnections implements FromRequestInterface, ListConnectionsInterface
{
    protected $limit;

    protected function __construct($limit)
    {
        $this->limit = $limit;
    }

    public function version(): int
    {
        return 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? 20;
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self($request->get('limit'));
    }
}
