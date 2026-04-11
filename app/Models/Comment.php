<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'content',
        'read_at',
        'read_by',
    ];

    protected $casts = [
        'read_by' => 'array',
        'read_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentionedUsers()
    {
        return $this->belongsToMany(User::class, 'comment_user');
    }

    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class);
    }

    public function canBeDeletedBy(User $user)
    {
        return $user->id === $this->user_id || $user->isAdmin();
    }

    public function markAsReadBy(User $user)
    {
        $readBy = $this->read_by ?? [];
        if (!in_array($user->id, $readBy)) {
            $readBy[] = $user->id;
            $this->update(['read_by' => $readBy]);
        }
    }

    public function isReadBy(User $user): bool
    {
        return in_array($user->id, $this->read_by ?? []);
    }

    public function hasAttachments(): bool
    {
        return $this->attachments()->count() > 0;
    }
}
