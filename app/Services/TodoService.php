<?php

namespace App\Services;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TodoService
{

    public function getAll(): AnonymousResourceCollection
    {
        $data = Todo::query()
            ->when(request()->has('search'), function (Builder $query) {
                $query->where('title', 'like', '%' . request('search') . '%')
                    ->orWhere('description', 'like', '%' . request('search') . '%');
            })
            ->when(request()->has('completed'), function (Builder $query) {
                $query->where('completed', request('completed'));
            })
            ->when(request()->has('sort_by'), function (Builder $query) {
                $query->orderBy(request('sort_by'), request('sort_order') ?? 'asc');
            })
            ->paginate(10);

        return TodoResource::collection($data);
    }

    public function getDetails(Todo $todo): TodoResource
    {
        return TodoResource::make($todo);
    }

    public function create(array $data): TodoResource
    {
        $todo = Todo::query()->create($data);

        return TodoResource::make($todo);
    }

    public function update(Todo $todo, array $newData): TodoResource
    {
        $todo->update($newData);

        return TodoResource::make($todo->refresh());
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }
}
