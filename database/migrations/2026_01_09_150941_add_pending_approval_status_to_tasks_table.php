<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM to add 'Pendiente de Aprobación'
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pendiente', 'En progreso', 'Pendiente de Aprobación', 'Completada') DEFAULT 'Pendiente'");

        // Add approval tracking columns
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'Pendiente de Aprobación' back to 'En progreso'
        DB::statement("UPDATE tasks SET status = 'En progreso' WHERE status = 'Pendiente de Aprobación'");

        // Remove the approval columns
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at']);
        });

        // Revert the ENUM
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pendiente', 'En progreso', 'Completada') DEFAULT 'Pendiente'");
    }
};
