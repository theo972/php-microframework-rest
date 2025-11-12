<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

final class User
{
    public function __construct(
        private readonly ?int $id,
        private string $email,
        private string $passwordHash,
        private readonly DateTime $createdAt,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
    public function createdAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setPasswordHash(string $hash): self
    {
        $this->passwordHash = $hash;
        return $this;
    }

    public static function hydrate(int $id, string $email, string $passwordHash, DateTime $createdAt): self
    {
        return new self($id, $email, $passwordHash, $createdAt);
    }

    public static function create(string $email, string $passwordHash): self
    {
        return new self(null, $email, $passwordHash, new DateTime());
    }

    public static function fromRow(array $row): self
    {
        return self::hydrate(
            (int) $row ['id'],
            (string) $row ['email'],
            (string) $row ['password_hash'],
            new DateTime((string) $row['created_at'])
        );
    }

    public function toInsertColumns(): array
    {
        return [
            'email'         => $this->email,
            'password_hash' => $this->passwordHash,
            'created_at'    => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
