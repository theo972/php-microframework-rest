<?php

declare(strict_types=1);

namespace App\Core\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class KeysChoice
{
    public function __construct(
        public array $allowedKeys,
        public ?string $message = null
    ) {
    }
}
