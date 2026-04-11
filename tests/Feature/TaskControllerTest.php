<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_assigned_tasks(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertSee($task->title);
    }

    public function test_user_cannot_view_others_tasks(): void
    {
        $user1 = User::factory()->create(['is_active' => true]);
        $user2 = User::factory()->create(['is_active' => true]);

        $task = Task::factory()->create([
            'assignee_id' => $user2->id,
            'creator_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->get(route('tasks.show', $task));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_all_tasks(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertSee($task->title);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $assignee = User::factory()->create(['is_active' => true]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'assignee_id' => $assignee->id,
            'priority' => 'Alta',
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'creator_id' => $user->id,
        ]);
    }

    public function test_user_cannot_assign_to_inactive_user(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'assignee_id' => $inactiveUser->id,
            'priority' => 'Alta',
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Test Task',
        ]);
    }

    public function test_user_can_update_assigned_task(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
            'status' => 'Pendiente',
        ]);

        $response = $this->actingAs($user)->patch(route('tasks.update', $task), [
            'status' => 'En progreso',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'En progreso',
        ]);
    }

    public function test_tasks_index_displays_assigned_tasks(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertSee($task->title);
    }

    public function test_search_filters_tasks(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $task1 = Task::factory()->create([
            'title' => 'Unique Search Term',
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $task2 = Task::factory()->create([
            'title' => 'Different Title',
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['search' => 'Unique']));

        $response->assertStatus(200);
        $response->assertSee('Unique Search Term');
        $response->assertDontSee('Different Title');
    }
}
