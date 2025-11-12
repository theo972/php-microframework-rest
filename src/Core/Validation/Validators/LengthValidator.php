<?php

declare(strict_types=1);

namespace App\Core\Validation\Validators;

use App\Core\Validation\Attributes\Length;
use App\Core\Validation\Interface\ConstraintValidator;

final class LengthValidator implements ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string
    {
        /** @var Length $constraint */
        if (!is_string($value)) {
            return $constraint->message ?? 'invalid_type';
        }
        $len = strlen($value);
        if ($constraint->min !== null && $len < $constraint->min) {
            return $constraint->message ?? 'min_'.$constraint->min;
        }
        if ($constraint->max !== null && $len > $constraint->max) {
            return $constraint->message ?? 'max_'.$constraint->max;
        }
        return null;
    }

    public static function constraintClass(): string
    {
        return Length::class;
    }
}
