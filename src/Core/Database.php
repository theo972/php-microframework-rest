<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    public static function connectDatabase(): PDO
    {
        $databaseUrl = $_ENV['DATABASE_URL'];
        $user        = $_ENV['DB_USER'] ?? 'root';
        $password    = $_ENV['DB_PASS'] ?? '';
        return new PDO($databaseUrl, $user, $password);
    }

    public static function migrate(PDO $pdo, string $sqlFile): void
    {
        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            throw new \RuntimeException("Migration file not readable: $sqlFile");
        }
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $query) {
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
    }
}
