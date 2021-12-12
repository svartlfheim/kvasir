<?php

namespace App\Connections\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Connections\DTO\API\V1\ListConnectionsDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiV1Controller
{
    #[Route('', name: 'list')]
    public function index(ListConnectionsDTO $dto): JsonResponse
    {
        return new JsonResponse([
            'limit' => $dto->getLimit(),
        ]);
    }
}
