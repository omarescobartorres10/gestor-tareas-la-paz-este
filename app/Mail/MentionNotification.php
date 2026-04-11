<?php

namespace App\Mail;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MentionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $mentionedUser,
        public User $sender,
        public Task $task,
        public Comment $comment
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💬 ' . $this->sender->name . ' te mencionó en "' . $this->task->title . '"',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mention',
        );
    }
}
