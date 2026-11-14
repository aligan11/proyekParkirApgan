<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Summary of successResponse
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse|mixed
     */
    protected function successResponse(mixed $data = null, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'message' => $message,
                'data' => $data,
            ],
            $code,
        );
    }
    /**
     * Summary of errorResponse
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse|mixed
     */
    protected function errorResponse(string $message = null, int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json(
            [
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ],
            $code,
        );
    }
    /**
     * Summary of resourceResponse
     * @param mixed $resource
     * @param string $message
     * @param int $code
     * @return JsonResponse|mixed
     */
    protected function resourceResponse(mixed $resource, string $message = null, int $code = 200): JsonResponse
    {
        return $this->successResponse($resource, $message, $code);
    }

    protected function collectionResponse(ResourceCollection $collection, string $message = null, int $code = 200): JsonResponse
    {
        $data = $collection->response()->getData();
        return $this->resourceResponse([
            'item' => $data->data ?? [],
            'meta' => [
                'current_page' => $data->meta->current_page ?? null,
                'last_page' => $data->meta->last_page ?? null,
                'per_page' => $data->meta->per_page ?? null,
                'total' => $data->meta->total ?? null,
                'from' => $data->meta->from ?? null,
                'to' => $data->meta->to ?? null,
                'path' => $data->meta->path ?? null,
            ],
            'link' => $data->links ?? null
        ],$message,$code);
    }

    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = null,
        int $code = 200
    ): JsonResponse {
        return $this->successResponse([
            'items' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'path' => $paginator->path(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], $message, $code);
    }

    protected function emptyResponse(
        string $message = null,
        int $code = 204
    ): JsonResponse {
        return response()->json(null, $code);
    }

}
