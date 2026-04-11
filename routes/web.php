<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('tasks.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/archived', [TaskController::class, 'archived'])->name('tasks.archived');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->middleware('throttle:20,1')->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->middleware('throttle:30,1')->name('tasks.update');
    Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
    Route::patch('/tasks/{task}/unarchive', [TaskController::class, 'unarchive'])->name('tasks.unarchive');
    Route::get('/tasks/{task}/comments', [TaskController::class, 'getComments'])->name('tasks.comments');
    Route::patch('/tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
    Route::patch('/tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');

    // Comments
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->middleware('throttle:30,1')->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Notifications - Rate limited for security
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/toggle-email', [\App\Http\Controllers\NotificationController::class, 'toggleEmailNotifications'])->name('notifications.toggle-email');
        Route::get('/api/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/api/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
    });

    // Chat Attachments - Rate limited to prevent abuse
    Route::middleware('throttle:30,1')->group(function () {
        Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\ChatAttachmentController::class, 'download'])
            ->name('attachments.download');
        Route::delete('/attachments/{attachment}', [\App\Http\Controllers\ChatAttachmentController::class, 'destroy'])
            ->name('attachments.destroy');
    });

    // User Search API (for @mention autocomplete)
    Route::get('/api/users/search', [\App\Http\Controllers\UserSearchController::class, 'search'])
        ->middleware('throttle:60,1')
        ->name('users.search');

    // Mark comments as read
    Route::post('/tasks/{task}/mark-read', [\App\Http\Controllers\CommentController::class, 'markAsRead'])
        ->name('tasks.mark-read');

    // Admin
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // User management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('create-user');
        Route::get('/users/{user}/activity-calendar', [AdminController::class, 'userActivityCalendar'])->name('user-activity-calendar');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('store-user');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('update-user');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('delete-user');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('toggle-user-status');

        // Task management
        Route::get('/tasks', [AdminController::class, 'allTasks'])->name('tasks');
        Route::patch('/tasks/{task}/reassign', [AdminController::class, 'reassignTask'])->name('reassign-task');

        // Reports & Analytics
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export-pdf', [AdminController::class, 'exportPDF'])->name('export-pdf');
        Route::get('/reports/export-csv', [AdminController::class, 'exportCSV'])->name('export-csv');
        Route::get('/analytics/data', [AdminController::class, 'analyticsData'])->name('analytics-data');
    });
});

require __DIR__ . '/auth.php';
