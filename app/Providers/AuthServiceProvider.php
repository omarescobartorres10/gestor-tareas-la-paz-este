<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\TaskPolicy;
use App\Policies\CommentPolicy;
use App\Policies\AdminPolicy;
use App\Models\Task;
use App\Models\Comment;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Task::class => TaskPolicy::class,
        Comment::class => CommentPolicy::class,
        User::class => AdminPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Admin tiene acceso absoluto a todo sin restricciones
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->role === 'admin') {
                return true; // Admin bypasses all authorization checks
            }
        });
    }
}
