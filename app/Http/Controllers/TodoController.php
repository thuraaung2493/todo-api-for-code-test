<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Services\ApiResponse;
use App\Services\TodoService;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    public function __construct(
        private ApiResponse $apiResponse,
        private TodoService $todoService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse->resourceResponse(
            data: $this->todoService->getAll(),
            message: 'Retrived all todos',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTodoRequest $request)
    {
        try {
            return $this->apiResponse->resourceResponse(
                data: $this->todoService->create($request->validated()),
                message: 'Todo created successfully',
                status: 201
            );
        } catch (\Throwable $th) {
            Log::error('Failed to create todo: ' . $th->getMessage());

            return $this->apiResponse->error('Failed to create todo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return $this->apiResponse->resourceResponse(
            data: $this->todoService->getDetails($todo),
            message: 'Retrieved todo details',
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        try {
            return $this->apiResponse->resourceResponse(
                data: $this->todoService->update($todo, $request->validated()),
                message: 'Todo updated successfully',
            );
        } catch (\Throwable $th) {
            Log::error('Failed to update todo: ' . $th->getMessage());

            return $this->apiResponse->error('Failed to update todo');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        try {
            $this->todoService->delete($todo);

            return $this->apiResponse->success(
                message: 'Todo deleted successfully',
            );
        } catch (\Throwable $th) {
            Log::error('Failed to delete todo: ' . $th->getMessage());

            return $this->apiResponse->error('Failed to delete todo');
        }
    }
}
