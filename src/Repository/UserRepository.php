<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\User;
use PDO;

final readonly class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function insert(User $user): int
    {
        $query = $this->pdo->prepare(
            'INSERT INTO users(email, password_hash, created_at) VALUES (:email, :hash, :created_at)'
        );
        $cols = $user->toInsertColumns();
        $query->execute([
            ':email'      => $cols['email'],
            ':hash'       => $cols['password_hash'],
            ':created_at' => $cols['created_at'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?User
    {
        $query = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $query->execute([':email' => $email]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row ? User::fromRow($row) : null;
    }
}
