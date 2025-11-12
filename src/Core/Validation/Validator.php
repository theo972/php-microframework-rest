<?php

declare(strict_types=1);

namespace App\Core\Validation;

final readonly class Validator
{
    public function __construct(private ConstraintValidatorRegistry $registry)
    {
    }

    public function validate(object $dto): array
    {
        $errors = [];

        $reflectionClass = new \ReflectionClass($dto);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName  = $property->getName();
            $propertyValue = $property->isInitialized($dto) ? $property->getValue($dto) : null;

            foreach ($property->getAttributes() as $attribute) {
                $constraintInstance = $attribute->newInstance();
                $constraint         = $attribute->getName();

                $validatorInstance = $this->registry->get($constraint);
                if (!$validatorInstance) {
                    continue;
                }

                $errorCode = $validatorInstance->validate($propertyValue, $constraintInstance, $dto, $propertyName);
                if ($errorCode !== null) {
                    $errors[$propertyName][] = $errorCode;
                }
            }
        }

        return $errors;
    }

}
