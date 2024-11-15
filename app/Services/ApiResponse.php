<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    private $data = null;
    private $message;
    private $status;

    public function success($data, $message = 'Success', $status = 200): JsonResponse
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;

        return $this->toResponse();
    }

    public function error($message = 'Something went wrong', $status = 500): JsonResponse
    {
        $this->message = $message;
        $this->status = $status;

        return $this->toResponse();
    }

    private function toResponse(): JsonResponse
    {
        return response()->json([
            'data' => $this->data,
            'message' => $this->message,
            'status' => $this->status,
        ]);
    }
}
