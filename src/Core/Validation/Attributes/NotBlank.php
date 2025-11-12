<?php

declare(strict_types=1);

namespace App\Core\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class NotBlank
{
    public function __construct(
        public ?string $message = null
    ) {
    }
}
