<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Storage path
            $table->string('file_type'); // image, document
            $table->string('mime_type'); // image/jpeg, application/pdf, etc.
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->timestamps();

            $table->index('comment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_attachments');
    }
};
