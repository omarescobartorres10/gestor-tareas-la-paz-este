<?php

namespace App\Http\Controllers;

use App\Models\ChatAttachment;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Mail\MentionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        try {
            // Check if there's file(s) or content
            $hasFile = $request->hasFile('attachments');
            $hasContent = $request->filled('content');

            // Must have at least content or file
            if (!$hasContent && !$hasFile) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes escribir un mensaje o adjuntar un archivo'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Debes escribir un mensaje o adjuntar un archivo');
            }

            // Validate
            $rules = [
                'content' => 'nullable|string|max:5000',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx|max:5120',
            ];

            $validated = $request->validate($rules);

            // Sanitize content
            $content = '';
            if (!empty($validated['content'])) {
                $content = strip_tags($validated['content']);
                $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
            }

            // Extract mentions
            $mentionedUserIds = [];
            $mentionedUsers = collect();

            if (!empty($content)) {
                preg_match_all('/@([\p{L}\p{N}_.-]+)/u', $content, $matches);

                $tokens = collect($matches[1] ?? [])
                    ->map(fn($t) => Str::lower(Str::ascii($t)))
                    ->unique()
                    ->values();

                if ($tokens->isNotEmpty()) {
                    $mentionedUsers = User::where('is_active', true)
                        ->where(function ($query) use ($tokens) {
                            $tokens->each(function ($token) use ($query) {
                                $query->orWhereRaw('LOWER(name) LIKE ?', ["{$token}%"])
                                    ->orWhereRaw('LOWER(SUBSTRING_INDEX(email, "@", 1)) = ?', [$token]);
                            });
                        })
                        ->limit($tokens->count())
                        ->get();

                    $mentionedUserIds = $mentionedUsers->pluck('id')->all();
                }
            }

            // Create comment
            $comment = $task->comments()->create([
                'user_id' => auth()->id(),
                'content' => $content,
            ]);

            // Handle file attachments
            if ($hasFile) {
                $files = $request->file('attachments');
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $this->handleAttachment($file, $comment);
                    }
                }
            }

            // Handle mentioned users and create notifications
            if (!empty($mentionedUserIds)) {
                foreach ($mentionedUsers as $user) {
                    $task->grantAccessTo($user, 'mentioned');

                    if ($user->id !== auth()->id()) {
                        Notification::create([
                            'user_id' => $user->id,
                            'type' => 'mention',
                            'task_id' => $task->id,
                            'comment_id' => $comment->id,
                            'message' => auth()->user()->name . ' te mencionó en "' . $task->title . '"',
                            'is_read' => false,
                        ]);

                        // Send email notification if user has enabled it (async - doesn't block response)
                        if ($user->email_notifications) {
                            try {
                                Mail::to($user->email)->queue(
                                    new MentionNotification($user, auth()->user(), $task, $comment)
                                );
                            } catch (\Exception $e) {
                                \Log::error('Failed to queue mention email: ' . $e->getMessage());
                            }
                        }
                    }
                }

                $comment->mentionedUsers()->sync($mentionedUserIds);
            }

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mensaje enviado correctamente',
                    'comment' => $comment->load(['user', 'attachments', 'mentionedUsers'])
                ]);
            }

            return redirect()->back()->with('success', 'Mensaje enviado');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            \Log::error('Comment store failed: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el mensaje'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al enviar el mensaje');
        }
    }

    private function handleAttachment($file, Comment $comment)
    {
        $mimeType = $file->getMimeType();
        $fileType = str_starts_with($mimeType, 'image/') ? 'image' : 'document';

        // Store file
        $path = $file->store('chat-attachments', 'public');

        // Create attachment record
        ChatAttachment::create([
            'comment_id' => $comment->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
        ]);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return redirect()->back()->with('success', 'Comentario eliminado');
    }

    /**
     * Mark all comments in a task as read by current user
     */
    public function markAsRead(Task $task)
    {
        $this->authorize('view', $task);

        // Get all comments from other users that haven't been read by current user
        $comments = $task->comments()
            ->where('user_id', '!=', auth()->id())
            ->get();

        foreach ($comments as $comment) {
            $comment->markAsReadBy(auth()->user());
        }

        return response()->json([
            'success' => true,
            'marked_count' => $comments->count()
        ]);
    }
}
