<?php

declare(strict_types=1);

namespace App\Core\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Choice
{
    public function __construct(
        public array $choices,
        public ?string $message = null
    ) {
    }
}
