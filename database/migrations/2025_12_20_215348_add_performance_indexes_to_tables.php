<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations - Add performance indexes
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Composite indexes for common queries (only new ones)
            $table->index(['status', 'due_date'], 'tasks_status_due_date_index');
            $table->index(['creator_id', 'status'], 'tasks_creator_status_index');
            $table->index(['assignee_id', 'status'], 'tasks_assignee_status_index');
            // archived_at and fulltext indexes already exist from previous migration
        });

        Schema::table('users', function (Blueprint $table) {
            // Indexes for filtering
            $table->index(['is_active', 'department'], 'users_active_dept_index');
            $table->index(['is_active', 'position'], 'users_active_position_index');
            $table->index(['is_active', 'role'], 'users_active_role_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            // Index for task comments ordering
            $table->index(['task_id', 'created_at'], 'comments_task_created_index');
            $table->index(['user_id'], 'comments_user_id_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Index for user notifications
            $table->index(['user_id', 'is_read', 'created_at'], 'notifications_user_read_created_index');
            $table->index(['task_id'], 'notifications_task_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_status_due_date_index');
            $table->dropIndex('tasks_creator_status_index');
            $table->dropIndex('tasks_assignee_status_index');
            // archived_at and fulltext indexes not created by this migration
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_active_dept_index');
            $table->dropIndex('users_active_position_index');
            $table->dropIndex('users_active_role_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_task_created_index');
            $table->dropIndex('comments_user_id_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_index');
            $table->dropIndex('notifications_task_id_index');
        });
    }
};
