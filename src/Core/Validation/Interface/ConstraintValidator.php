<?php

declare(strict_types=1);

namespace App\Core\Validation\Interface;

interface ConstraintValidator
{
    public function validate(mixed $value, object $constraint, object $dto, string $property): ?string;
    public static function constraintClass(): string;
}
