<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_can_view_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($creator)->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_assignee_can_view_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($assignee)->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.show', $task));

        $response->assertStatus(200);
    }

    public function test_unrelated_user_cannot_view_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($otherUser)->get(route('tasks.show', $task));

        $response->assertStatus(403);
    }

    public function test_creator_can_update_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create(['creator_id' => $creator->id]);

        $response = $this->actingAs($creator)->patch(route('tasks.update', $task), [
            'status' => 'En progreso',
        ]);

        $response->assertRedirect();
    }

    public function test_assignee_can_update_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($assignee)->patch(route('tasks.update', $task), [
            'status' => 'Completada',
        ]);

        $response->assertRedirect();
    }

    public function test_unrelated_user_cannot_update_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($otherUser)->patch(route('tasks.update', $task), [
            'status' => 'Completada',
        ]);

        $response->assertStatus(403);
    }

    public function test_creator_can_archive_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create(['creator_id' => $creator->id]);

        $response = $this->actingAs($creator)->patch(route('tasks.archive', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);

        $task->refresh();
        $this->assertNotNull($task->archived_at);
    }

    public function test_admin_can_archive_any_task(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $creator = User::factory()->create();

        $task = Task::factory()->create(['creator_id' => $creator->id]);

        $response = $this->actingAs($admin)->patch(route('tasks.archive', $task));

        $response->assertRedirect();

        $task->refresh();
        $this->assertNotNull($task->archived_at);
    }

    public function test_assignee_cannot_archive_task(): void
    {
        $creator = User::factory()->create();
        $assignee = User::factory()->create();

        $task = Task::factory()->create([
            'creator_id' => $creator->id,
            'assignee_id' => $assignee->id,
        ]);

        $response = $this->actingAs($assignee)->patch(route('tasks.archive', $task));

        $response->assertStatus(403);
    }
}
