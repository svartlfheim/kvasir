<?php

namespace App\Connections\Controller;

use App\Common\Bus\CommandBus;
use App\Connections\DTO\V1\ListConnections;
use App\Connections\DTO\V1\CreateConnection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Connections\Command\CreateConnection as CreateConnectionCommand;
use App\Connections\Command\ListConnections as ListConnectionsCommand;

class ApiV1Controller extends AbstractController
{
    protected CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /*
    Not sure where to put the HEAD reqeust for list and create...
    Will look into this later, likely to cause some weird behaviour...
    */
    #[Route('', name: 'list', methods: ['GET', 'HEAD'])]
    public function index(ListConnections $dto): JsonResponse
    {
        $this->bus->dispatch(new ListConnectionsCommand($dto));
        return new JsonResponse([
            'limit' => $dto->getLimit(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST', 'HEAD'])]
    public function create(CreateConnection $dto): JsonResponse
    {
        $this->bus->dispatch(new CreateConnectionCommand($dto));

        return new JsonResponse([
            'chosen_name' => $dto->getName(),
            'chosen_engine' => $dto->getEngine(),
        ]);
    }
}
