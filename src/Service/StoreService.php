<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Validation\Validator;
use App\Models\SearchStoresDto;
use App\Models\StoreDto;
use App\Repository\StoreRepository;

final readonly class StoreService
{
    public function __construct(
        private StoreRepository $storeRepository,
        private Validator $validator
    ) {
    }

    public function get(int $id): array
    {
        $response = [
            'error'  => [],
            'store'  => [],
            'status' => true,
        ];

        $store = $this->storeRepository->find($id);
        if (!$store) {
            $response['status'] = false;
            $response['error']  = ['id' => 'not_found'];
            return $response;
        }

        $response['store'] = $store;
        return $response;
    }

    public function search(array $payload): array
    {
        $response = [
            'errors' => [],
            'stores' => [],
            'status' => true,
            'meta'   => [],
        ];

        $searchStoreDto = SearchStoresDto::fromArray($payload);
        $errors = $this->validator->validate($searchStoreDto);
        if (!empty($errors)) {
            $response['status'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        [$items, $total] = $this->storeRepository->search($searchStoreDto);

        $response['stores'] = $items;
        $response['meta']   = [
            'page'  => $searchStoreDto->page,
            'size'  => $searchStoreDto->size,
            'total' => $total,
        ];

        return $response;
    }



    public function create(array $payload): array
    {
        $response = [
            'errors' => [],
            'store'  => [],
            'status' => true
        ];
        $store  = StoreDto::fromArray($payload);
        $errors = $this->validator->validate($store);
        if (!empty($errors)) {
            $response['status'] = false;
            $response['errors'] = $errors;
            return $response;
        }
        $id = $this->storeRepository->insert(
            $store->name,
            $store->city,
            $store->address,
            $store->phone
        );

        $response['store'] = [
            'id'         => $id,
            'name'       => $store->name,
            'city'       => $store->city,
            'address'    => $store->address,
            'phone'      => $store->phone,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return $response;
    }

    public function update(int $id, array $payload): array
    {
        $response = [
            'errors' => [],
            'store'  => [],
            'status' => true,
        ];

        $storeDb = $this->storeRepository->find($id);
        if (!$storeDb) {
            $response['status'] = false;
            $response['errors'] = ['id' => 'not_found'];
            return $response;
        }

        $store  = StoreDto::updateFromArray($payload, $storeDb);
        $errors = $this->validator->validate($store);
        if (!empty($errors)) {
            $response['status'] = false;
            $response['errors'] = $errors;
            return $response;
        }

        $storeUpdate = [];
        if ($store->name !== $storeDb['name']) {
            $storeUpdate['name'] = $store->name;
        }
        if ($store->city !== $storeDb['city']) {
            $storeUpdate['city'] = $store->city;
        }
        if ($store->address !== $storeDb['address']) {
            $storeUpdate['address'] = $store->address;
        }
        if ($store->phone !== $storeDb['phone']) {
            $storeUpdate['phone'] = $store->phone;
        }

        if (!empty($storeUpdate)) {
            $this->storeRepository->update($id, $storeUpdate);
            $storeDb = $this->storeRepository->find($id);
        }

        $response['store'] = $storeDb;
        return $response;
    }
    public function delete(int $id): array
    {
        $response = [
            'errors' => [],
            'store'  => [],
            'status' => true,
        ];

        $store = $this->storeRepository->find($id);
        if (!$store) {
            $response['status'] = false;
            $response['errors'] = ['id' => 'not_found'];
            return $response;
        }

        $this->storeRepository->delete($id);
        $response['store'] = $store;

        return $response;
    }
}
