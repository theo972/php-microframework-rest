<?php

declare(strict_types=1);

namespace App\Core\Validation\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Email
{
    public function __construct(
        public ?string $message = null
    ) {
    }
}
