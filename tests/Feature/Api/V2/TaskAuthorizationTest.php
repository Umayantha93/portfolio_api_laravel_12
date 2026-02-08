<?php

namespace Tests\Feature\Api\V2;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_tasks(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->for($owner)->create();

        $this->actingAs($owner)
            ->getJson('/api/v2/tasks/' . $task->id)
            ->assertOk()
            ->assertJsonFragment(['name' => $task->name]);

    }

    public function test_user_cannot_view_tasks_owned_by_others(): void
    {
        //Arrange: create 2 users and a task for each user
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->for($owner)->create();

        //Act: login as user1 and try to view task2
        $this->actingAs($otherUser)
            ->getJson('/api/v2/tasks/' . $task->id)
            //Assert: see if we get 403 forbidden
            ->assertForbidden();
    }

    public function test_user_can_update_owned_tasks(): void
    {
        //Arrange: create a user and a task for that user
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->for($user)->create();

        //Act: try to update the task
        $response = $this->putJson('/api/v2/tasks/' . $task->id, [
            'name' => 'Updated Task Name',
            'is_completed' => true,
        ]);

        //Assert: see if we get 200 OK and the task is updated
        $response->assertJsonFragment([
            'name' => 'Updated Task Name',
        ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
        ]);
    }

    public function test_user_cannot_update_tasks_owned_by_others(): void
    {
        //Arrange: create 2 users and a task for each user
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->for($owner)->create();

        //Act: login as user1 and try to update task2
        $this->actingAs($otherUser)
            ->putJson('/api/v2/tasks/' . $task->id, [
                'name' => 'Updated Task Name',
                'is_completed' => true,
            ])
            //Assert: see if we get 403 forbidden
            ->assertForbidden();
    }

    public function test_user_can_delete_own_tasks(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->for($user)->create();

        $this->deleteJson('/api/v2/tasks/' . $task->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_cannot_delete_tasks_owned_by_others()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();
    
        $this->actingAs($otherUser)
            ->deleteJson("/api/v2/tasks/{$task->id}")
            ->assertForbidden();
    }
    
    public function test_user_cannot_complete_tasks_owned_by_others()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();
    
        $payload = ['is_completed' => true];
    
        $this->actingAs($otherUser)
            ->patchJson("/api/v2/tasks/{$task->id}/complete", $payload)
            ->assertForbidden();
    }
}
