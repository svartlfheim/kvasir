<?php

namespace App\Connections\Controller;

use App\Common\RequiresMessageBus;
use App\Common\MessageBusInterface;
use App\Connections\Command\V1\ListConnections;
use Symfony\Component\Routing\Annotation\Route;
use App\Connections\Command\V1\CreateConnection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use App\Connections\API\ListConnectionsJSONResponseBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function create(CreateConnection $cmd): JsonResponse
    {
        $res = $this->bus->dispatchAndGetResult($cmd);

        return new JsonResponse($res);
    }
}
