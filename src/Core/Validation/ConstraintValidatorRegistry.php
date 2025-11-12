<?php

declare(strict_types=1);

namespace App\Core\Validation;

use App\Core\Validation\Interface\ConstraintValidator;

final class ConstraintValidatorRegistry
{
    private array $validators = [];

    /** @param iterable<ConstraintValidator> $validators */
    public function __construct(iterable $validators = [])
    {
        foreach ($validators as $validator) {
            $this->validators[$validator::constraintClass()] = $validator;
        }
    }

    public function get(string $validator): ?ConstraintValidator
    {
        return $this->validators[$validator] ?? null;
    }

    public function register(ConstraintValidator $validator): void
    {
        $this->validators[$validator::constraintClass()] = $validator;
    }
}
