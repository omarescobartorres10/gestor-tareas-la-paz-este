<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_comment_to_assigned_task(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('comments.store', $task), [
            'content' => 'Test comment content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'Test comment content',
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_others_comment(): void
    {
        $user1 = User::factory()->create(['is_active' => true]);
        $user2 = User::factory()->create(['is_active' => true]);

        $task = Task::factory()->create([
            'assignee_id' => $user1->id,
            'creator_id' => $user1->id,
        ]);

        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->delete(route('comments.destroy', $comment));

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_admin_can_delete_any_comment(): void
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

        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_comment_respects_max_length(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
        ]);

        $longContent = str_repeat('a', 5001); // Exceeds 5000 char limit

        $response = $this->actingAs($user)->post(route('comments.store', $task), [
            'content' => $longContent,
        ]);

        $response->assertSessionHasErrors('content');
    }
}
