<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class Auth
{
    public static function requireBasic(Request $request, PDO $pdo): array
    {
        $headers = (string) $request->headers->get('Authorization', '');
        if (!preg_match('/^Basic\s+([A-Za-z0-9+\/=]+)$/', $headers, $basicAuthHeader)) {
            self::unauthorized('BASIC_REQUIRED');
        }

        $decoded = base64_decode($basicAuthHeader[1], true);
        if ($decoded === false || !str_contains($decoded, ':')) {
            self::unauthorized('BAD_BASIC_HEADER');
        }

        [$email, $password] = explode(':', $decoded, 2);
        $email = mb_strtolower(trim($email));
        $password = rtrim($password, "\r\n");

        $query = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $query->execute([':email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            self::unauthorized('BAD_CREDENTIALS');
        }

        return [
            'id'    => (int) $user['id'],
            'email' => $user['email']
        ];
    }

    private static function unauthorized(string $code): never
    {
        header('WWW-Authenticate: Basic realm="api", charset="UTF-8"');
        (new JsonResponse(['error' => ['code' => $code]], 401))->send();
        exit;
    }
}
