<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    public function get_user_can_get_list_of_tasks(): void
    {
        //Arrange: create 2 fake tasks
        $tasks = Task::factory()->count(2)->create();

        //Act: make request to the endpoint
        $response = $this->getJson('/api/v1/tasks');

        //Assert: see if we get the tasks back
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'is_completed']
            ]
        ]);

    }


    public function test_user_can_get_single_task(): void
    {
        //Arrange: create a fake task
        $task = Task::factory()->create();

        //Act: make request to the endpoint
        $response = $this->getJson('/api/v1/tasks/' . $task->id);

        //Assert: see if we get the task back
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'is_completed']
        ]);

        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'name' => $task->name,
                'is_completed' => $task->is_completed,
            ]
        ]);
    }

    // `POST /tasks` - Create a new task
    public function test_user_can_create_a_tast(): void
    {
        $response = $this->postJson('api/v1/tasks', [
            'name' => 'New Task'
        ]);

        // this checks for 201 status code
        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => ['id', 'name', 'is_completed']
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
        ]);

    }

    // `POST /tasks` - Create a new task with invalid data
    public function test_user_cannot_create_invalid_task(): void
    {
        $response = $this->postJson('api/v1/tasks', [
            'name' => '', // invalid name
        ]);
        
        // this checks for 422 status code
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);

    }

    // `PUT /tasks/{id}` - Update an existing task
    public function test_user_can_update_a_task(): void
    {
        //Arrange: create a fake task
        $task = Task::factory()->create();

        //Act: make request to the endpoint
        $response = $this->putJson('/api/v1/tasks/' . $task->id, [
            'name' => 'Updated Task Name',
        ]); 
        //Assert: see if we get the updated task back
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Updated Task Name',
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
        ]);
    }

    // `PUT /tasks/{id}` - Update an existing task with empty data
    public function test_user_cannot_update_a_task_with_invalid_data(): void
    {
        //Arrange: create a fake task
        $task = Task::factory()->create();

        //Act: make request to the endpoint
        $response = $this->putJson('/api/v1/tasks/' . $task->id, [
            'name' => '', // invalid name
        ]); 
        //Assert: see if we get validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    // `PATCH /tasks/{id}/complete` - Mark a task as completed
    public function test_user_can_mark_task_as_completed(): void
    {
    
        //Arrange: create a fake task
        $task = Task::factory()->create([
            'is_completed' => false,
        ]);

        //Act: make request to the endpoint
        $response = $this->patchJson('/api/v1/tasks/' . $task->id . '/complete', [
            'is_completed' => true,
        ]); 
        //Assert: see if we get the updated task back
        $response->assertOk();
        $response->assertJsonFragment([
            'is_completed' => true,
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_completed' => true,
        ]);
    }

    // `PATCH /tasks/{id}/complete` - Mark a task as completed with invalid data
    public function test_user_cannot_mark_task_as_completed_with_invalid_data(): void
    {
        //Arrange: create a fake task
        $task = Task::factory()->create();
        //Act: make request to the endpoint
        $response = $this->patchJson('/api/v1/tasks/' . $task->id . '/complete', [
            'is_completed' => 'yes', // invalid data
        ]);

        //Assert: see if we get validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['is_completed']);
    }

    // `DELETE /tasks/{id}` - Delete a task
    public function test_user_can_delete_a_task(): void
    {
        //Arrange: create a fake task
        $task = Task::factory()->create();

        //Act: make request to the endpoint
        $response = $this->deleteJson('/api/v1/tasks/' . $task->id);

        //Assert: see if the task is deleted
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}