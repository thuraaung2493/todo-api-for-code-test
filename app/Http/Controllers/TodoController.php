<?php

namespace App\Http\Controllers;

use App\Http\Responses\CollectionResponse;
use App\Models\Todo;
use App\Services\ApiResponse;
use App\Services\TodoService;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        //
    }
}
