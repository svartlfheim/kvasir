<?php

namespace App\Connections\Command\V1;

use App\Common\Attributes\HTTPField;
use App\Common\Command\FromRequestInterface;
use App\Connections\Command\ListConnectionsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ListConnections implements FromRequestInterface, ListConnectionsInterface
{
    /** @todo: Add an integration tests for the validation */

    #[Assert\Type('int')]
    #[Assert\Range(
        min: 10,
        max: 100,
    )]
    #[HTTPField('page_size')]
    protected $pageSize;

    #[Assert\Type('int')]
    protected $page;

    #[Assert\Type('string')]
    #[Assert\Choice(['name', 'engine'])]
    #[HTTPField('order_field')]
    protected $orderField;

    #[Assert\Type('string')]
    #[Assert\Choice(['asc', 'desc'])]
    #[HTTPField('order_direction')]
    protected $orderDirection;

    protected function __construct($pageSize, $page, $orderField, $orderDirection)
    {
        $this->pageSize = $pageSize;
        $this->page = $page;
        $this->orderField = $orderField;
        $this->orderDirection = $orderDirection;
    }

    public function version(): int
    {
        return 1;
    }

    public function getPageSize(): int
    {
        return $this->pageSize ?? 20;
    }

    public function getPage(): string
    {
        return $this->page ?? '';
    }

    public function getOrderField(): string
    {
        return $this->orderField ?? 'name';
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection ?? 'asc';
    }

    public static function fromRequest(Request $request): mixed
    {
        return new self(
            $request->get('page_size'),
            $request->get('page'),
            $request->get('order_field'),
            $request->get('order_direction'),
        );
    }
}
