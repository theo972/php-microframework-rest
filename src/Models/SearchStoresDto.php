<?php

namespace App\Models;

use App\Core\Validation\Attributes\Choice;
use App\Core\Validation\Attributes\KeysChoice;

final class SearchStoresDto
{
    #[KeysChoice(['id','name','city','address','phone','created_at','updated_at'])]
    public array $filters = [];

    #[Choice(['id','name','city','address','phone','created_at','updated_at'])]
    public string $order_field = 'created_at';

    #[Choice(['ASC','DESC'])]
    public string $order_dir = 'DESC';

    public int $page = 1;

    public int $size = 10;

    public static function fromArray(array $payload): self
    {
        $searchDto = new self();

        $searchDto->filters = isset($payload['filters']) && is_array($payload['filters']) ? $payload['filters'] : [];
        if (!empty($payload['order']) && is_array($payload['order'])) {
            $searchDto->order_field = (string)($payload['order']['field'] ?? $searchDto->order_field);
            $searchDto->order_dir   = strtoupper((string)($payload['order']['direction'] ?? $searchDto->order_dir));
        }
        if (isset($payload['page'])) {
            $searchDto->page = (int)$payload['page'];
        }
        if (isset($payload['size'])) {
            $searchDto->size = (int)$payload['size'];
        }
        return $searchDto;
    }
}
