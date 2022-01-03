<?php

namespace App\Connections\Controller;

use App\Common\DI\RequiresMessageBus;
use App\Connections\Command\V1\CreateConnection;
use App\Connections\Command\V1\ListConnections;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller uses a prefix...
 *
 * @see /config/routes/annotations.yaml
 */
class ApiV1Controller extends AbstractController
{
    use RequiresMessageBus;

    /** @see \App\Connections\Handler\ListConnections */
    #[Route('', name: 'list', methods: ['GET', 'HEAD'])]
    public function index(ListConnections $cmd): JsonResponse
    {
        return $this->bus->dispatchAndGetResult($cmd)->json();
    }

    /** @see \App\Connections\Handler\CreateConnection */
    #[Route('', name: 'create', methods: ['POST', 'HEAD'])]
    public function create(CreateConnection $cmd): JsonResponse
    {
        return $this->bus->dispatchAndGetResult($cmd)->json();
    }
}
