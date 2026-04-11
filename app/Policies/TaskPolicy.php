<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->creator_id
            || $user->id === $task->assignee_id
            || $task->mentionedUsers->contains($user->id)
            || $user->isAdmin();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->creator_id
            || $user->id === $task->assignee_id
            || $task->mentionedUsers->contains($user->id)
            || $user->isAdmin();
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->creator_id || $user->isAdmin();
    }

    /**
     * Determine if the user can approve a task.
     * Only the task creator or admin can approve.
     */
    public function approve(User $user, Task $task): bool
    {
        return $user->id === $task->creator_id || $user->isAdmin();
    }
}
