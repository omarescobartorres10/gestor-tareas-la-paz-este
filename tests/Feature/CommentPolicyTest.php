<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['creator_id' => $user->id, 'assignee_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_others_comment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['creator_id' => $user1->id, 'assignee_id' => $user1->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->delete(route('comments.destroy', $comment));

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $task = Task::factory()->create(['creator_id' => $user->id, 'assignee_id' => $user->id]);
        $comment = Comment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
