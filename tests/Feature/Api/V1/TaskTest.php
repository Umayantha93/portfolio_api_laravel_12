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
}
