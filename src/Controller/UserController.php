<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Attributes\Route;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserController
{
    public function __construct(
        private Request $request,
        private UserService $userService
    ) {
    }

    #[Route('/users', methods: ['POST'])]
    public function create(): JsonResponse
    {
        $body = json_decode($this->request->getContent(), true);
        if (!is_array($body)) {
            return new JsonResponse(['errors' => ['body' => 'invalid_json']], 400);
        }
        $response = $this->userService->create($body);

        return new JsonResponse(
            ['data' => $response],
            $response['status'] ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }
}
