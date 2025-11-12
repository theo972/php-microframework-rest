<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Validation\Attributes\Length;
use App\Core\Validation\Attributes\NotBlank;

final class StoreDto
{
    #[NotBlank]
    #[Length(min:2, max:100)]
    public ?string $name = null;

    #[NotBlank]
    #[Length(min:1, max:64)]
    public ?string $city = null;

    #[Length(min:1, max:255)]
    public ?string $address = null;

    #[Length(min:3, max:32)]
    public ?string $phone = null;

    public static function fromArray(array $storeArray): self
    {
        $store          = new self();
        $store->name    = isset($storeArray['name']) ? trim((string)$storeArray['name']) : null;
        $store->city    = isset($storeArray['city']) ? trim((string)$storeArray['city']) : null;
        $store->address = isset($storeArray['address']) ? trim((string)$storeArray['address']) : null;
        $store->phone   = isset($storeArray['phone']) ? trim((string)$storeArray['phone']) : null;
        return $store;
    }

    public static function updateFromArray(array $payload, array $storeArray): self
    {
        $dto = new self();

        $dto->name = array_key_exists('name', $payload)
            ? (isset($payload['name']) ? trim((string)$payload['name']) : null)
            : (isset($storeArray['name']) ? trim((string)$storeArray['name']) : null);

        $dto->city = array_key_exists('city', $payload)
            ? (isset($payload['city']) ? trim((string)$payload['city']) : null)
            : (isset($storeArray['city']) ? trim((string)$storeArray['city']) : null);

        $dto->address = array_key_exists('address', $payload)
            ? (isset($payload['address']) ? trim((string)$payload['address']) : null)
            : (array_key_exists('address', $storeArray) && $storeArray['address'] !== null ? trim((string)$storeArray['address']) : null);

        $dto->phone = array_key_exists('phone', $payload)
            ? (isset($payload['phone']) ? trim((string)$payload['phone']) : null)
            : (array_key_exists('phone', $storeArray) && $storeArray['phone'] !== null ? trim((string)$storeArray['phone']) : null);

        return $dto;
    }
}
