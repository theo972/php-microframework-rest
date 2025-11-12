<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Validation\Attributes\Email;
use App\Core\Validation\Attributes\Length;
use App\Core\Validation\Attributes\NotBlank;

final class UserDto
{
    public function __construct(
        #[NotBlank]
        #[Email]
        public string $email,
        #[NotBlank]
        #[Length(min: 8, max: 128)]
        public string $password
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: trim((string)($data['email'] ?? '')),
            password: (string)($data['password'] ?? '')
        );
    }
}
