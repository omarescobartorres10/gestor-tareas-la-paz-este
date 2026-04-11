<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('content');
            $table->json('read_by')->nullable()->after('read_at'); // Array of user IDs who read this message
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['read_at', 'read_by']);
        });
    }
};
