<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Índices para filtros frecuentes
            $table->index('status');
            $table->index('priority');
            $table->index('archived_at');

            // Índices ya existen como foreign keys, pero aseguramos están optimizados
            // assignee_id y creator_id ya tienen índices por ser foreign keys
        });

        // Add FULLTEXT index using raw SQL (not available in Blueprint)
        \DB::statement('ALTER TABLE tasks ADD FULLTEXT INDEX tasks_fulltext_search(title, description)');

        Schema::table('users', function (Blueprint $table) {
            // Índice para filtrado de usuarios activos
            $table->index('is_active');
        });

        Schema::table('comments', function (Blueprint $table) {
            // task_id ya tiene índice por ser foreign key
            // Añadir índice compuesto para queries comunes
            $table->index(['task_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['archived_at']);
        });

        // Drop FULLTEXT index
        \DB::statement('ALTER TABLE tasks DROP INDEX tasks_fulltext_search');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['task_id', 'created_at']);
        });
    }
};
