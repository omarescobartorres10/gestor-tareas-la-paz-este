<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        // Log admin dashboard access
        \Log::info('Admin dashboard accessed', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Cache dashboard statistics for 5 minutes
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            $totalUsers = User::where('is_active', true)->count();
            $totalTasks = Task::count();
            $completedTasks = Task::where('status', 'Completada')->count();
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            // Tasks by status for pie chart
            $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Tasks by priority for bar chart
            $tasksByPriority = Task::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();

            // Productivity by user (top 10)
            $userProductivity = User::where('is_active', true)
                ->withCount([
                    'tasksAssigned',
                    'tasksAssigned as completed_tasks_count' => function ($query) {
                        $query->where('status', 'Completada');
                    }
                ])
                ->orderBy('completed_tasks_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'assigned' => $user->tasks_assigned_count,
                        'completed' => $user->completed_tasks_count,
                    ];
                });

            // Temporal trends (last 30 days)
            $trends = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $trends['labels'][] = Carbon::parse($date)->format('d/m');
                $trends['created'][] = Task::whereDate('created_at', $date)->count();
                $trends['completed'][] = Task::whereDate('updated_at', $date)
                    ->where('status', 'Completada')
                    ->count();
            }

            // Overdue and urgent tasks
            $overdueTasks = Task::where('status', '!=', 'Completada')
                ->where('due_date', '<', Carbon::now())
                ->with(['assignee', 'creator'])
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get();

            $urgentTasks = Task::where('status', '!=', 'Completada')
                ->where('priority', 'Alta')
                ->where('due_date', '>=', Carbon::now())
                ->where('due_date', '<=', Carbon::now()->addDays(3))
                ->with(['assignee', 'creator'])
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get();

            // Eager load counts to prevent N+1 queries
            $usersWithStats = User::where('is_active', true)
                ->withCount(['tasksCreated', 'tasksAssigned', 'comments'])
                ->with([
                    'tasksAssigned' => function ($query) {
                        $query->where('status', 'Completada');
                    },
                    'tasksAssigned.comments' => function ($query) {
                        $query->orderBy('created_at', 'asc');
                    }
                ])
                ->get();

            $userStats = $usersWithStats->map(function ($user) {
                $tasksCompletedCount = $user->tasksAssigned->count();
                $avgComments = $user->tasks_assigned_count > 0 ? $user->comments_count / $user->tasks_assigned_count : 0;

                $avgResponseTime = $user->tasksAssigned
                    ->map(function ($task) {
                        if ($task->comments->isNotEmpty()) {
                            return $task->created_at->diffInMinutes($task->comments->first()->created_at);
                        }
                        return null;
                    })
                    ->filter()
                    ->avg() ?? 0;

                return [
                    'user' => $user,
                    'tasks_created' => $user->tasks_created_count,
                    'tasks_assigned' => $user->tasks_assigned_count,
                    'tasks_completed' => $tasksCompletedCount,
                    'avg_comments' => round($avgComments, 2),
                    'avg_response_time' => round($avgResponseTime, 0),
                ];
            });

            return compact(
                'totalUsers',
                'totalTasks',
                'completionRate',
                'tasksByStatus',
                'tasksByPriority',
                'userProductivity',
                'trends',
                'overdueTasks',
                'urgentTasks',
                'userStats'
            );
        });

        return view('admin.dashboard', $stats);
    }

    public function users()
    {
        $users = User::withCount(['tasksCreated', 'tasksAssigned', 'comments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Get activity calendar data for a specific user (JSON)
     */
    public function userActivityCalendar(User $user)
    {
        $activityCalendar = [];

        // Get dates when tasks were assigned to user
        $assignedDates = Task::where('assignee_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE(created_at) as activity_date, count(*) as count')
            ->groupBy('activity_date')
            ->pluck('count', 'activity_date')
            ->toArray();

        // Get dates when tasks were completed by user
        $completedDates = Task::where('assignee_id', $user->id)
            ->where('status', 'Completada')
            ->whereNotNull('updated_at')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE(updated_at) as activity_date, count(*) as count')
            ->groupBy('activity_date')
            ->pluck('count', 'activity_date')
            ->toArray();

        // Merge both arrays (sum counts for same dates)
        foreach ($assignedDates as $date => $count) {
            $activityCalendar[$date] = ($activityCalendar[$date] ?? 0) + $count;
        }
        foreach ($completedDates as $date => $count) {
            $activityCalendar[$date] = ($activityCalendar[$date] ?? 0) + $count;
        }

        // User stats
        $stats = [
            'tasks_assigned' => Task::where('assignee_id', $user->id)->count(),
            'tasks_completed' => Task::where('assignee_id', $user->id)->where('status', 'Completada')->count(),
            'tasks_created' => Task::where('creator_id', $user->id)->count(),
        ];

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department,
                'position' => $user->position,
            ],
            'activityCalendar' => $activityCalendar,
            'stats' => $stats,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorize('manageUsers', User::class);

        \Log::info('Admin accessed user management', [
            'admin_id' => auth()->id(),
            'ip' => request()->ip()
        ]);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'employee_id' => 'nullable|string|max:20|unique:users,employee_id,' . $user->id,
            'role' => 'required|in:admin,usuario',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'can_assign_tasks' => 'sometimes|boolean',
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = [
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ];
        }

        $validated = $request->validate($rules, [
            'password.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial (@$!%*#?&)',
        ]);
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id() && isset($validated['is_active']) && !$validated['is_active']) {
            return redirect()->back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // Prevent removing admin role from last admin
        if (isset($validated['role']) && $validated['role'] !== 'admin' && $user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'No puedes quitar privilegios al último administrador.');
            }
        }

        $user->update($validated);

        // Clear cache when user data changes
        Cache::forget('admin.dashboard.stats');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario actualizado']);
        }

        return redirect()->back()->with('success', 'Usuario actualizado');
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $this->authorize('manageUsers', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users', new \App\Rules\GovernmentEmail()],
            'employee_id' => 'nullable|string|max:20|unique:users,employee_id',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // at least one lowercase
                'regex:/[A-Z]/',      // at least one uppercase  
                'regex:/[0-9]/',      // at least one digit
                'regex:/[@$!%*#?&]/', // at least one special char
            ],
            'role' => 'required|in:admin,usuario',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'can_assign_tasks' => 'sometimes|boolean',
        ], [
            'password.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial (@$!%*#?&)',
            'employee_id.unique' => 'Esta cédula de identidad ya está registrada.',
        ]);
        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        // Clear cache
        Cache::forget('admin.dashboard.stats');

        return redirect()->route('admin.users')->with('success', 'Usuario creado exitosamente');
    }

    public function deleteUser(User $user)
    {
        $this->authorize('deleteUser', [User::class, $user]);

        \Log::warning('User deletion attempted', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'ip' => request()->ip()
        ]);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Prevent deleting the last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return redirect()->back()->with('error', 'No puedes eliminar al último administrador.');
            }
        }

        // Soft delete - just deactivate
        $user->update(['is_active' => false]);

        \Log::warning('User deleted successfully', [
            'admin_id' => auth()->id(),
            'deleted_user_email' => $user->email
        ]);

        // Clear cache
        Cache::forget('admin.dashboard.stats');

        return redirect()->back()->with('success', 'Usuario desactivado exitosamente');
    }

    public function toggleUserStatus(User $user)
    {
        $this->authorize('toggleUserStatus', [User::class, $user]);

        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes cambiar el estado de tu propia cuenta.');
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        \Log::info('User status toggled', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'old_status' => $oldStatus,
            'new_status' => $user->is_active,
            'ip' => request()->ip()
        ]);

        // Clear cache
        Cache::forget('admin.dashboard.stats');

        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario {$status} exitosamente");
    }

    public function allTasks(Request $request)
    {
        $this->authorize('viewAllTasks', User::class);

        $query = Task::with(['creator', 'assignee', 'comments']);

        // Filters
        if ($request->has('creator_id') && $request->creator_id) {
            $query->where('creator_id', $request->creator_id);
        }

        if ($request->has('assignee_id') && $request->assignee_id) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('due_date', '<=', $request->date_to);
        }

        // Archived filter
        if ($request->has('archived') && $request->archived === '1') {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        $tasks = $query->orderBy('due_date', 'desc')->paginate(15);
        $users = User::where('is_active', true)->get();

        return view('admin.tasks', compact('tasks', 'users'));
    }

    public function reassignTask(Request $request, Task $task)
    {
        $this->authorize('reassignTask', User::class);

        $validated = $request->validate([
            'assignee_id' => 'required|exists:users,id',
        ]);

        $oldAssignee = $task->assignee->name;
        $task->update($validated);
        $newAssignee = $task->fresh()->assignee->name;

        // Add system comment
        $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => "🔄 reasignó la tarea de **{$oldAssignee}** a **{$newAssignee}**",
            'task_id' => $task->id
        ]);

        // Clear cache
        Cache::forget('admin.dashboard.stats');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Tarea reasignada']);
        }

        return redirect()->back()->with('success', 'Tarea reasignada exitosamente');
    }

    public function reports(Request $request)
    {
        // Filtro de fechas
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Statistics for reports
        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'Completada')->count(),
            'pending_tasks' => Task::where('status', 'Pendiente')->count(),
            'in_progress_tasks' => Task::where('status', 'En progreso')->count(),
        ];

        // Productividad por usuario
        $userProductivity = $this->getUserProductivity();

        // Estadísticas por departamento
        $departmentStats = $this->getDepartmentStats();

        // Análisis de tiempos
        $timeAnalysis = $this->getTimeAnalysis();

        // Tendencia de tareas (últimos 14 días)
        $tasksTrend = $this->getTasksTrend(14);

        // Tareas por estado
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Tareas por prioridad
        $tasksByPriority = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return view('admin.reports', compact(
            'stats',
            'userProductivity',
            'departmentStats',
            'timeAnalysis',
            'tasksTrend',
            'tasksByStatus',
            'tasksByPriority',
            'startDate',
            'endDate'
        ));
    }

    public function analyticsData(Request $request)
    {
        // Return fresh data without cache for real-time updates
        $totalUsers = User::where('is_active', true)->count();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'Completada')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Tasks by status for chart
        $tasksByStatus = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Tasks by priority for chart
        $tasksByPriority = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return response()->json([
            'kpis' => [
                'totalUsers' => $totalUsers,
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'completionRate' => $completionRate,
            ],
            'tasksByStatus' => $tasksByStatus,
            'tasksByPriority' => $tasksByPriority,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    private function getTasksTrend($days)
    {
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $trend[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'created' => Task::whereDate('created_at', $date)->count(),
                'completed' => Task::whereDate('updated_at', $date)
                    ->where('status', 'Completada')
                    ->count(),
            ];
        }
        return $trend;
    }

    private function getUserProductivity()
    {
        return User::where('is_active', true)
            ->withCount([
                'tasksAssigned',
                'tasksAssigned as completed_count' => function ($query) {
                    $query->where('status', 'Completada');
                }
            ])
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'assigned' => $user->tasks_assigned_count,
                    'completed' => $user->completed_count,
                    'rate' => $user->tasks_assigned_count > 0
                        ? round(($user->completed_count / $user->tasks_assigned_count) * 100, 2)
                        : 0,
                ];
            });
    }

    private function getDepartmentStats()
    {
        return User::where('is_active', true)
            ->whereNotNull('department')
            ->get()
            ->groupBy('department')
            ->map(function ($users, $department) {
                $totalAssigned = $users->sum(fn($u) => $u->tasksAssigned()->count());
                $totalCompleted = $users->sum(fn($u) => $u->tasksAssigned()->where('status', 'Completada')->count());

                return [
                    'department' => $department,
                    'users' => $users->count(),
                    'tasks_assigned' => $totalAssigned,
                    'tasks_completed' => $totalCompleted,
                    'completion_rate' => $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100, 2) : 0,
                ];
            })
            ->values();
    }

    private function getTimeAnalysis()
    {
        $now = Carbon::now();

        // Tareas vencidas
        $overdueTasks = Task::where('due_date', '<', $now)
            ->where('status', '!=', 'Completada')
            ->count();

        // Tareas que vencen pronto (próximos 3 días)
        $dueSoonTasks = Task::whereBetween('due_date', [$now, $now->copy()->addDays(3)])
            ->where('status', '!=', 'Completada')
            ->count();

        // Tiempo promedio de completar tareas (en días)
        $completedTasks = Task::where('status', 'Completada')
            ->whereNotNull('start_date')
            ->get();

        $avgCompletionTime = 0;
        if ($completedTasks->count() > 0) {
            $totalDays = $completedTasks->sum(function ($task) {
                return Carbon::parse($task->start_date)->diffInDays($task->updated_at);
            });
            $avgCompletionTime = round($totalDays / $completedTasks->count(), 1);
        }

        // Tasa de cumplimiento (completadas antes de vencer)
        $completedOnTime = Task::where('status', 'Completada')
            ->whereColumn('updated_at', '<=', 'due_date')
            ->count();

        $totalCompleted = Task::where('status', 'Completada')->count();
        $onTimeRate = $totalCompleted > 0 ? round(($completedOnTime / $totalCompleted) * 100, 1) : 0;

        return [
            'overdue_tasks' => $overdueTasks,
            'due_soon_tasks' => $dueSoonTasks,
            'avg_completion_days' => $avgCompletionTime,
            'on_time_rate' => $onTimeRate,
            'completed_on_time' => $completedOnTime,
            'completed_late' => $totalCompleted - $completedOnTime,
        ];
    }

    public function exportPDF()
    {
        // Recopilar todos los datos
        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'Completada')->count(),
            'pending_tasks' => Task::where('status', 'Pendiente')->count(),
            'in_progress_tasks' => Task::where('status', 'En progreso')->count(),
        ];

        $userProductivity = $this->getUserProductivity();
        $departmentStats = $this->getDepartmentStats();
        $timeAnalysis = $this->getTimeAnalysis();
        $generatedAt = Carbon::now()->format('d/m/Y H:i');

        // Generar HTML para PDF
        $html = view('admin.reports-pdf', compact(
            'stats',
            'userProductivity',
            'departmentStats',
            'timeAnalysis',
            'generatedAt'
        ))->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="reporte-tareas-' . date('Y-m-d') . '.html"');
    }

    public function exportCSV()
    {
        $tasks = Task::with(['creator', 'assignee'])->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="tareas-export-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, ['ID', 'Título', 'Estado', 'Prioridad', 'Creador', 'Asignado', 'Fecha Inicio', 'Fecha Límite', 'Creado']);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->status,
                    $task->priority,
                    $task->creator->name ?? 'N/A',
                    $task->assignee->name ?? 'N/A',
                    $task->start_date?->format('Y-m-d'),
                    $task->due_date?->format('Y-m-d'),
                    $task->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
