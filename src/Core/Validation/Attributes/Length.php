<?php

declare(strict_types=1);

namespace App\Core\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Length
{
    public function __construct(
        public ?int $min = null,
        public ?int $max = null,
        public ?string $message = null
    ) {
    }
}
