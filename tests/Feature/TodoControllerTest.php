<?php

use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\mock;
use function Pest\Laravel\putJson;

describe('TodoControllerTest::index', function () {
    it('should return a list of todos with pagination', function () {
        Todo::factory(20)->create();

        getJson(route('todos.index'))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 10)
                    ->has(
                        'data.0',
                        fn(AssertableJson $json) => $json
                            ->hasAll(['id', 'title', 'description', 'completed', 'createdAt'])
                    )
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should search by title', function () {
        Todo::factory(10)->create();
        $todo = Todo::factory()->create(['title' => 'Test Todo']);

        getJson(route('todos.index', ['search' => 'Test']))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 1)
                    ->has(
                        'data.0',
                        fn(AssertableJson $json) => $json
                            ->hasAll(['id', 'title', 'description', 'completed', 'createdAt'])
                            ->where('id', $todo->id)
                            ->where('title', $todo->title)
                            ->etc()
                    )
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should search by description', function () {
        Todo::factory(10)->create();
        $todo = Todo::factory()->create(['description' => 'Test Todo']);

        getJson(route('todos.index', ['search' => 'Test']))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 1)
                    ->has(
                        'data.0',
                        fn(AssertableJson $json) => $json
                            ->hasAll(['id', 'title', 'description', 'completed', 'createdAt'])
                            ->where('id', $todo->id)
                            ->where('title', $todo->title)
                            ->etc()
                    )
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should filter by completed', function () {
        Todo::factory(10)->create(['completed' => false]);
        Todo::factory(5)->create(['completed' => true]);

        getJson(route('todos.index', ['completed' => true]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 5)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );

        getJson(route('todos.index', ['completed' => false]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 10)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted in accending order without sort order', function () {
        $todo1 = Todo::factory()->create(['created_at' => now()->subDays(2)]);
        $todo2 = Todo::factory()->create(['created_at' => now()->subDay()]);
        $todo3 = Todo::factory()->create(['created_at' => now()]);

        getJson(route('todos.index', ["sort_by" => "created_at"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo1->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo3->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by created_at in ascending order', function () {
        $todo1 = Todo::factory()->create(['created_at' => now()->subDays(2)]);
        $todo2 = Todo::factory()->create(['created_at' => now()->subDay()]);
        $todo3 = Todo::factory()->create(['created_at' => now()]);

        getJson(route('todos.index', ["sort_by" => "created_at", "sort_order" => "asc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo1->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo3->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by created_at in descding order', function () {
        $todo1 = Todo::factory()->create(['created_at' => now()->subDays(2)]);
        $todo2 = Todo::factory()->create(['created_at' => now()->subDay()]);
        $todo3 = Todo::factory()->create(['created_at' => now()]);

        getJson(route('todos.index', ["sort_by" => "created_at", "sort_order" => "desc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo3->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo1->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by title in ascending order', function () {
        $todo1 = Todo::factory()->create(['title' => 'A']);
        $todo2 = Todo::factory()->create(['title' => 'B']);
        $todo3 = Todo::factory()->create(['title' => 'C']);

        getJson(route('todos.index', ["sort_by" => "title", "sort_order" => "asc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo1->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo3->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by title in descding order', function () {
        $todo1 = Todo::factory()->create(['title' => 'A']);
        $todo2 = Todo::factory()->create(['title' => 'B']);
        $todo3 = Todo::factory()->create(['title' => 'C']);

        getJson(route('todos.index', ["sort_by" => "title", "sort_order" => "desc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo3->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo1->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by description in ascending order', function () {
        $todo1 = Todo::factory()->create(['description' => 'A']);
        $todo2 = Todo::factory()->create(['description' => 'B']);
        $todo3 = Todo::factory()->create(['description' => 'C']);

        getJson(route('todos.index', ["sort_by" => "description", "sort_order" => "asc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo1->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo3->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });

    it('should retrieve todos sorted by description in descding order', function () {
        $todo1 = Todo::factory()->create(['description' => 'A']);
        $todo2 = Todo::factory()->create(['description' => 'B']);
        $todo3 = Todo::factory()->create(['description' => 'C']);

        getJson(route('todos.index', ["sort_by" => "description", "sort_order" => "desc"]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'meta', 'links', 'message', 'status'])
                    ->where('data', fn(Collection $data) => $data->count() === 3)
                    ->has('data', 3)
                    ->where('data.0.id', $todo3->id)
                    ->where('data.1.id', $todo2->id)
                    ->where('data.2.id', $todo1->id)
                    ->where('message', 'Retrived all todos')
                    ->where('status', 200)
            );
    });
});

describe('TodoControllerTest::store', function () {
    it('should create todo', function () {
        $todo = Todo::factory()->make();

        postJson(
            uri: route('todos.index'),
            data: [
                'title' => $todo->title,
                'description' => $todo->description,
            ]
        )
            ->assertStatus(201)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.title', $todo->title)
                    ->where('data.description', $todo->description)
                    ->where('data.completed', false)
                    ->where('message', 'Todo created successfully')
                    ->where('status', 201)
            );

        assertDatabaseCount('todos', 1);
        assertDatabaseHas('todos', [
            'title' => $todo->title,
            'description' => $todo->description,
            'completed' => false,
        ]);
    });

    it('should create todo without description', function () {
        $title = 'Test Todo';

        postJson(
            uri: route('todos.index'),
            data: [
                'title' => $title
            ]
        )
            ->assertStatus(201)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.title', $title)
                    ->where('data.description', null)
                    ->where('data.completed', false)
                    ->where('message', 'Todo created successfully')
                    ->where('status', 201)
            );

        assertDatabaseCount('todos', 1);
        assertDatabaseHas('todos', [
            'title' => $title,
            'description' => null,
            'completed' => false,
        ]);
    });

    it('cannot create todo without title', function () {
        $todo = Todo::factory()->make();

        postJson(
            uri: route('todos.index'),
            data: [
                'description' => $todo->description,
            ]
        )
            ->assertStatus(422)
            ->assertInvalid(['title']);

        assertDatabaseCount('todos', 0);
    });

    it('should return 500 if todo creation fails', function () {
        $todo = Todo::factory()->make();

        mock(TodoService::class)
            ->shouldReceive('create')
            ->andThrow(new Exception('Error creating todo'));

        postJson(
            uri: route('todos.index'),
            data: [
                'title' => $todo->title,
                'description' => $todo->description,
            ]
        )
            ->assertStatus(500)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data', null)
                    ->where('message', 'Failed to create todo')
                    ->where('status', 500)
            );

        assertDatabaseCount('todos', 0);
    });
});

describe('TodoControllerTest::show', function () {
    it('should retrieve todo by id', function () {
        $todo = Todo::factory()->create();

        getJson(route('todos.show', ['todo' => $todo->id]))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.id', $todo->id)
                    ->where('data.title', $todo->title)
                    ->where('data.description', $todo->description)
                    ->where('data.completed', $todo->completed)
                    ->where('message', 'Retrieved todo details')
                    ->where('status', 200)
            );
    });

    it('should return 404 if todo not found', function () {
        getJson(route('todos.show', ['todo' => Str::uuid()->toString()]))
            ->assertStatus(404)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data', null)
                    ->where('message', 'Resource not found')
                    ->where('status', 404)
            );
    });
});

describe('TodoControllerTest::update', function () {
    it('should update todo with new title', function () {
        $todo = Todo::factory()->create();
        $newTitle = 'New Todo';

        putJson(
            uri: route('todos.update', ['todo' => $todo->id]),
            data: [
                'title' => $newTitle,
            ]
        )
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.title', $newTitle)
                    ->where('data.description', $todo->description)
                    ->where('data.completed', $todo->completed)
                    ->where('message', 'Todo updated successfully')
                    ->where('status', 200)
            );

        assertDatabaseCount('todos', 1);
        assertDatabaseHas('todos', [
            'title' => $newTitle,
            'description' => $todo->description,
            'completed' => $todo->completed,
        ]);
    });

    it('should update todo with new description', function () {
        $todo = Todo::factory()->create();
        $newDescription = 'New Description';

        putJson(
            uri: route('todos.update', ['todo' => $todo->id]),
            data: [
                'description' => $newDescription,
            ]
        )
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.title', $todo->title)
                    ->where('data.description', $newDescription)
                    ->where('data.completed', $todo->completed)
                    ->where('message', 'Todo updated successfully')
                    ->where('status', 200)
            );

        assertDatabaseCount('todos', 1);
        assertDatabaseHas('todos', [
            'title' => $todo->title,
            'description' => $newDescription,
            'completed' => $todo->completed,
        ]);
    });

    it('should update todo with new status', function () {
        $todo = Todo::factory()->create();
        $newStatus = true;  // completed

        putJson(
            uri: route('todos.update', ['todo' => $todo->id]),
            data: [
                'completed' => $newStatus,
            ]
        )
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data.title', $todo->title)
                    ->where('data.description', $todo->description)
                    ->where('data.completed', $newStatus)
                    ->where('message', 'Todo updated successfully')
                    ->where('status', 200)
            );

        assertDatabaseCount('todos', 1);
        assertDatabaseHas('todos', [
            'title' => $todo->title,
            'description' => $todo->description,
            'completed' => $newStatus,
        ]);
    });

    it('should return 404 if todo not found', function () {
        putJson(route('todos.update', ['todo' => Str::uuid()->toString()]))
            ->assertStatus(404)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data', null)
                    ->where('message', 'Resource not found')
                    ->where('status', 404)
            );
    });

    it('should return 500 if todo update fails', function () {
        $todo = Todo::factory()->create();

        mock(TodoService::class)
            ->shouldReceive('update')
            ->andThrow(new Exception('Error updating todo'));

        putJson(route('todos.update', ['todo' => $todo->id]))
            ->assertStatus(500)
            ->assertJson(
                fn(AssertableJson $json) => $json
                    ->hasAll(['data', 'message', 'status'])
                    ->where('data', null)
                    ->where('message', 'Failed to update todo')
                    ->where('status', 500)
            );
    });
});
