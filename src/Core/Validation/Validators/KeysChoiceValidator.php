<?php

declare(strict_types=1);

namespace App\Core\Validation\Validators;

use App\Core\Validation\Attributes\KeysChoice;
use App\Core\Validation\Interface\ConstraintValidator;

final class KeysChoiceValidator implements ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string
    {
        /** @var KeysChoice $constraint */
        if (!is_array($value)) {
            return $constraint->message ?? 'invalid_type';
        }
        foreach (array_keys($value) as $key) {
            if (!in_array($key, $constraint->allowedKeys, true)) {
                return $constraint->message ?? 'invalid_keys';
            }
        }
        return null;
    }

    public static function constraintClass(): string
    {
        return KeysChoice::class;
    }
}
