<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'assignee_id',
        'status',
        'priority',
        'start_date',
        'due_date',
        'archived_at',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'due_date' => 'datetime',
            'archived_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at'); // Check if archived_at is not null
    }

    public function scopeUnarchived($query)
    {
        return $query->whereNull('archived_at'); // Check if archived_at is null
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function mentionedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withTimestamps()
            ->withPivot('access_type');
    }

    public function grantAccessTo(User $user, string $accessType = 'mentioned')
    {
        if (!$this->mentionedUsers->contains($user->id)) {
            $this->mentionedUsers()->attach($user->id, ['access_type' => $accessType]);
        }
    }

    public function hasAccess(User $user): bool
    {
        return $user->id === $this->creator_id
            || $user->id === $this->assignee_id
            || $this->mentionedUsers->contains($user->id)
            || $user->isAdmin();
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'Pendiente de Aprobación';
    }

    public function getTimeRemainingAttribute()
    {
        $now = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);

        if ($dueDate->isPast()) {
            return 'Vencida';
        }

        $diff = $now->diff($dueDate);

        if ($diff->days > 0) {
            return "{$diff->days}d {$diff->h}h {$diff->i}m";
        }

        return "{$diff->h}h {$diff->i}m {$diff->s}s";
    }

    public function isOverdue()
    {
        return Carbon::parse($this->due_date)->isPast()
            && $this->status !== 'Completada'
            && $this->status !== 'Pendiente de Aprobación';
    }

    public function isDueSoon()
    {
        $daysUntilDue = Carbon::now()->diffInDays($this->due_date, false);
        return $daysUntilDue <= 2 && $daysUntilDue > 0
            && $this->status !== 'Completada'
            && $this->status !== 'Pendiente de Aprobación';
    }
}
