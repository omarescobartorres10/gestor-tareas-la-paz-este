<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }

    public function getUrl(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileIcon(): string
    {
        $mimeType = $this->mime_type;

        if (str_starts_with($mimeType, 'application/pdf')) {
            return 'pdf';
        } elseif (str_contains($mimeType, 'word')) {
            return 'word';
        } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
            return 'excel';
        } elseif (str_starts_with($mimeType, 'text/')) {
            return 'alt';
        }

        return 'file';
    }
}
