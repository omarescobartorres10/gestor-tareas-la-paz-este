<?php

namespace App\Http\Controllers;

use App\Models\ChatAttachment;
use Illuminate\Support\Facades\Storage;

class ChatAttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download(ChatAttachment $attachment)
    {
        // Verify user has access to the task
        $task = $attachment->comment->task;
        $this->authorize('view', $task);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroy(ChatAttachment $attachment)
    {
        // Only comment author or admin can delete attachments
        $comment = $attachment->comment;
        $this->authorize('delete', $comment);

        // Delete file from storage
        Storage::disk('public')->delete($attachment->file_path);

        // Delete record
        $attachment->delete();

        return redirect()->back()->with('success', 'Archivo eliminado');
    }
}
