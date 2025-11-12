<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class AuthController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $user = Auth::requireBasic($request, $this->pdo);
        return new JsonResponse([
            'data' => [
                'id'    => $user['id'],
                'email' => $user['email'],
            ]
        ], 200);
    }
}
