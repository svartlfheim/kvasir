<?php

namespace App\Connections\Controller\API\V1;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController
{
    #[Route('', name: 'list')]
    public function index(): JsonResponse
    {
        return new JsonResponse(['hello' => 'world']);
    }
}
