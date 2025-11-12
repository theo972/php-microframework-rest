<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\SearchStoresDto;
use PDO;
use PDOStatement;

final class StoreRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function find(int $id): ?array
    {
        $query = $this->db->prepare('SELECT * FROM stores WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function insert(string $name, string $city, ?string $address, ?string $phone): int
    {
        $query = $this->db->prepare(
            'INSERT INTO stores (name, city, address, phone, created_at, updated_at)
             VALUES (:name, :city, :address, :phone, NOW(), NOW())'
        );
        $query->execute([
            ':name'    => $name,
            ':city'    => $city,
            ':address' => $address,
            ':phone'   => $phone,
        ]);
        return (int) $this->db->lastInsertId();
    }
    public function update(int $id, array $set): void
    {
        $updateFields = [];
        $values       = [];

        foreach ($set as $column => $value) {
            $updateFields[] = "$column = ?";
            $values[]       = $value;
        }

        $updateFields[] = 'updated_at = NOW()';
        $values[]       = $id;

        $sqlQuery = 'UPDATE stores SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
        $query    = $this->db->prepare($sqlQuery);
        $query->execute($values);
    }


    public function delete(int $id): void
    {
        $query = $this->db->prepare('DELETE FROM stores WHERE id = ?');
        $query->execute([$id]);
    }

    public function search(SearchStoresDto $dto): array
    {
        [$where, $bindings] = $this->buildWhereFromDto($dto);
        $offset             = ($dto->page - 1) * $dto->size;

        $sql = "SELECT id, name, city, address, phone, created_at, updated_at
                FROM stores
                $where
                ORDER BY {$dto->order_field} {$dto->direction}
                LIMIT :limit OFFSET :offset";

        $query = $this->db->prepare($sql);
        $this->bindParams($query, $bindings);
        $query->bindValue(':limit', $dto->size, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->execute();
        $stores = $query->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $countSql = "SELECT COUNT(*) AS c FROM stores $where";
        $count    = $this->db->prepare($countSql);
        $this->bindParams($count, $bindings);
        $count->execute();
        $total = (int)($count->fetchColumn() ?: 0);

        return [$stores, $total];
    }

    private function buildWhereFromDto(SearchStoresDto $dto): array
    {
        $conditions = [];
        $bindings   = [];

        $filters = $dto->filters;

        if (!empty($filters['q'])) {
            $conditions[]   = '(name LIKE :q OR city LIKE :q OR address LIKE :q OR phone LIKE :q)';
            $bindings[':q'] = '%' . $filters['q'] . '%';
        }

        foreach (['name','city','address','phone'] as $column) {
            if (isset($filters[$column]) && $filters[$column] !== '') {
                $conditions[]         = "$column LIKE :$column";
                $bindings[":$column"] = '%' . $filters[$column] . '%';
            }
        }
        $where = $conditions ? ('WHERE '.implode(' AND ', $conditions)) : '';
        return [$where, $bindings];
    }

    private function bindParams(PDOStatement $query, array $bindings): void
    {
        foreach ($bindings as $param => $value) {
            $query->bindValue($param, $value);
        }
    }
}
