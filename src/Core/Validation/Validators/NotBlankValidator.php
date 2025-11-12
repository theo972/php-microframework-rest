<?php

declare(strict_types=1);

namespace App\Core\Validation\Validators;

use App\Core\Validation\Attributes\NotBlank;
use App\Core\Validation\Interface\ConstraintValidator;

final class NotBlankValidator implements ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string
    {
        /** @var NotBlank $constraint */
        if ($value === null) {
            return $constraint->message ?? 'blank';
        }
        if (is_string($value) && trim($value) === '') {
            return $constraint->message ?? 'blank';
        }
        return null;
    }

    public static function constraintClass(): string
    {
        return NotBlank::class;
    }
}
