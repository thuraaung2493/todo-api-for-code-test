<?php

namespace App\Services;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResponse
{
    private JsonResource|AnonymousResourceCollection|ResourceCollection|null $data = null;
    private string $message;
    private int $status;

    public function success(
        JsonResource|AnonymousResourceCollection|ResourceCollection $data,
        $message = 'Success',
        $status = 200
    ): Responsable {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;

        return $this->toResourceResponse();
    }

    public function message(string $message = 'Success', int $status = 200): JsonResponse
    {
        $this->message = $message;
        $this->status = $status;

        return $this->toJsonResponse();
    }

    public function error(string $message = 'Something went wrong', int $status = 500): JsonResponse
    {
        $this->message = $message;
        $this->status = $status;

        return $this->toJsonResponse();
    }

    private function toResourceResponse(): Responsable
    {
        return $this->data->additional([
            'message' => $this->message,
            'status' => $this->status
        ]);
    }

    private function toJsonResponse(): JsonResponse
    {
        return response()->json([
            'data' => $this->data,
            'message' => $this->message,
            'status' => $this->status,
        ], $this->status);
    }
}
