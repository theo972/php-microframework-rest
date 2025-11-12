<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Attributes\Route;
use App\Service\StoreService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class StoreController
{
    public function __construct(private Request $request, private StoreService $storeService)
    {

    }

    #[Route('/stores/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $response = $this->storeService->get($id);
        return new JsonResponse(['data' => $response], 200);
    }

    #[Route('/stores/search', methods: ['POST'])]
    public function search(): JsonResponse
    {
        $body = json_decode((string)$this->request->getContent(), true);
        if (!is_array($body)) {
            return new JsonResponse(['error' => ['body' => 'invalid_json']], 400);
        }

        $response = $this->storeService->search($body);
        return new JsonResponse([
            'data' => $response,
        ], $response['status'] ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    #[Route('/stores', methods: ['POST'])]
    public function create(): JsonResponse
    {
        $body = json_decode((string)$this->request->getContent(), true);
        if (!is_array($body)) {
            return new JsonResponse(['errors' => ['body' => 'invalid_json']], 400);
        }
        $response = $this->storeService->create($body);
        return new JsonResponse(
            ['data' => $response],
            $response['status'] ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/stores/{id}', methods: ['PUT'])]
    public function update(int $id): JsonResponse
    {
        $body = json_decode((string) $this->request->getContent(), true);
        if (!is_array($body)) {
            return new JsonResponse(['errors' => ['body' => 'invalid_json']], 400);
        }
        $response = $this->storeService->update($id, $body);
        return new JsonResponse(
            ['data' => $response],
            $response['status'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/stores/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $response = $this->storeService->delete($id);
        return new JsonResponse(['data' => $response], $response['status'] ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

}
