<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'password',
        'role',
        'department',
        'position',
        'is_active',
        'can_assign_tasks',
        'email_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model - Add cache invalidation
     */
    protected static function booted(): void
    {
        // Invalidate user caches when user is saved or deleted
        static::saved(function () {
            \Cache::forget('users.active');
            \Cache::forget('departments.active');
            \Cache::forget('positions.active');
            \Cache::forget('admin.dashboard.stats');
        });

        static::deleted(function () {
            \Cache::forget('users.active');
            \Cache::forget('departments.active');
            \Cache::forget('positions.active');
            \Cache::forget('admin.dashboard.stats');
        });
    }

    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function mentionedInTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')
            ->withTimestamps()
            ->withPivot('access_type');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
