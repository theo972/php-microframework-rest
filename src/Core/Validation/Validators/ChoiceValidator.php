<?php

declare(strict_types=1);

namespace App\Core\Validation\Validators;

use App\Core\Validation\Attributes\Choice;
use App\Core\Validation\Interface\ConstraintValidator;

final class ChoiceValidator implements ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string
    {
        /** @var Choice $constraint */
        if (!is_string($value) && !is_int($value)) {
            return $constraint->message ?? 'invalid_type';
        }
        if (!in_array($value, $constraint->choices, true)) {
            return $constraint->message ?? 'invalid_choice';
        }
        return null;
    }

    public static function constraintClass(): string
    {
        return Choice::class;
    }
}
