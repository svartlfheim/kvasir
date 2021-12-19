<?php

namespace App\Connections\Controller;

use App\Common\DI\RequiresMessageBus;
use App\Connections\API\CreateConnectionJSONResponseBuilder;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
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

    #[Route('', name: 'list', methods: ['GET', 'HEAD'])]
    public function index(ListConnections $cmd, ListConnectionsJSONResponseBuilder $responseBuilder): JsonResponse
    {
        return $responseBuilder->fromCommandResponse(
            $this->bus->dispatchAndGetResult($cmd)
        );
    }

    #[Route('', name: 'create', methods: ['POST', 'HEAD'])]
    public function create(CreateConnection $cmd, CreateConnectionJSONResponseBuilder $responseBuilder): JsonResponse
    {
        return $responseBuilder->fromCommandResponse(
            $this->bus->dispatchAndGetResult($cmd)
        );
    }
}
