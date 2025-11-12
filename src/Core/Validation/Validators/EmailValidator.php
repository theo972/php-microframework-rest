<?php

declare(strict_types=1);

namespace App\Core\Validation\Validators;

use App\Core\Validation\Attributes\Email;
use App\Core\Validation\Interface\ConstraintValidator;

final class EmailValidator implements ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string
    {
        /** @var Email $constraint */
        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $constraint->message ?? 'invalid_email';
        }
        return null;
    }

    public static function constraintClass(): string
    {
        return Email::class;
    }
}
