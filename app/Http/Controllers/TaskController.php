<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use App\Rules\ActiveUser;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Task::unarchived();

        // Filtro por vista - Default: mostrar todas las tareas
        $view = $request->query('view', 'all');
        if ($view === 'my_tasks') {
            // Tasks assigned to me
            $query->where('assignee_id', $user->id);
        } elseif ($view === 'my_tracking') {
            // Tasks created by me
            $query->where('creator_id', $user->id);
        } elseif ($view === 'all') {
            // All tasks I have access to (created, assigned, or mentioned)
            if (!$user->isAdmin()) {
                $query->where(function ($q) use ($user) {
                    $q->where('creator_id', $user->id)
                        ->orWhere('assignee_id', $user->id)
                        ->orWhereHas('mentionedUsers', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                });
            }
            // Admins see all tasks without restrictions
        }

        // Filtros adicionales
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->query('priority'));
        }
        if ($request->filled('department')) {
            $query->whereHas('assignee', function ($q) {
                $q->where('department', request()->query('department'));
            });
        }
        if ($request->filled('position')) {
            $query->whereHas('assignee', function ($q) {
                $q->where('position', request()->query('position'));
            });
        }

        // Full-text search optimization (uses MySQL MATCH AGAINST)
        if ($request->has('search') && $request->query('search')) {
            $search = $request->query('search');

            // Use full-text search if available, fallback to LIKE
            try {
                $query->whereRaw(
                    'MATCH(title, description) AGAINST(? IN BOOLEAN MODE)',
                    [$search]
                );
            } catch (\Exception $e) {
                // Fallback to LIKE if full-text index doesn't exist
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
        }


        // Optimized eager loading - only load what's needed
        $tasks = $query->withCount('comments') // Get actual total count
            ->with([
                'creator:id,name',  // Only select needed columns
                'assignee:id,name,department',
                'comments' => function ($q) {
                    $q->select('id', 'task_id', 'user_id', 'created_at')
                        ->latest()
                        ->limit(3); // Only last 3 comments for preview
                },
                'comments.user:id,name'
            ])
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        // Cache active users for 1 hour
        $activeUsers = \Cache::remember('users.active', 3600, function () {
            return User::where('is_active', true)
                ->select('id', 'name', 'email', 'department', 'position')
                ->get();
        });

        // Cache departments and positions for 1 hour
        $departments = \Cache::remember('departments.active', 3600, function () {
            return User::where('is_active', true)
                ->distinct()
                ->pluck('department')
                ->filter();
        });

        $positions = \Cache::remember('positions.active', 3600, function () {
            return User::where('is_active', true)
                ->distinct()
                ->pluck('position')
                ->filter();
        });

        return view('tasks.index', compact('tasks', 'activeUsers', 'departments', 'positions'));
    }

    public function archived(Request $request)
    {
        $user = auth()->user();

        // Get archived tasks
        $query = Task::whereNotNull('archived_at');

        // Filter by access for non-admin users
        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('creator_id', $user->id)
                    ->orWhere('assignee_id', $user->id)
                    ->orWhereHas('mentionedUsers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            });
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->with([
            'creator:id,name',
            'assignee:id,name',
            'comments' => function ($q) {
                $q->select('id', 'task_id')->limit(1); // Just count
            }
        ])
            ->select('tasks.*')
            ->orderBy('archived_at', 'desc')
            ->paginate(15);

        return view('tasks.archived', compact('tasks'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignee_id' => ['required', 'exists:users,id', new ActiveUser()],
            'priority' => 'required|in:Baja,Media,Alta',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['creator_id'] = auth()->id();

        $task = Task::create($validated);

        // Notify assigned user (don't notify if assigning to yourself)
        if ($task->assignee_id !== auth()->id()) {
            Notification::create([
                'user_id' => $task->assignee_id,
                'type' => 'assigned',
                'task_id' => $task->id,
                'message' => auth()->user()->name . ' te asignó la tarea "' . $task->title . '"',
                'is_read' => false,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Tarea creada exitosamente',
                'task' => $validated // Return validated data, or the full task object if needed.
            ], 201); // 201 Created status
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        // Load task with relationships
        $task->load(['creator:id,name', 'assignee:id,name,email,department']);

        // Cache active users
        $activeUsers = \Cache::remember('users.active', 3600, function () {
            return User::where('is_active', true)
                ->select('id', 'name', 'email')
                ->get();
        });

        // Optimize comments query
        $comments = $task->comments()
            ->with('user:id,name', 'mentionedUsers:id,name', 'attachments:id,comment_id,file_name,file_path,mime_type')
            ->select('comments.*')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('tasks.show', compact('task', 'activeUsers', 'comments'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|nullable',
            'assignee_id' => ['sometimes', 'exists:users,id', new ActiveUser()],
            'priority' => 'sometimes|in:Baja,Media,Alta',
            'start_date' => 'sometimes|date',
            'due_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'sometimes|in:Pendiente,En progreso,Pendiente de Aprobación,Completada',
        ]);

        // Status change permissions and approval workflow
        if (isset($validated['status']) && $validated['status'] !== $task->status) {
            $user = auth()->user();
            $isAdmin = $user->role === 'admin';
            $isCreator = $user->id === $task->creator_id;
            $isAssignee = $user->id === $task->assignee_id;

            // Only admin, creator, and assignee can change status
            if (!$isAdmin && !$isCreator && !$isAssignee) {
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'No tienes permiso para cambiar el estado de esta tarea'
                    ], 403);
                }
                return redirect()->back()->withErrors(['status' => 'No tienes permiso para cambiar el estado de esta tarea']);
            }

            // APPROVAL WORKFLOW: If assignee tries to mark as "Completada", 
            // redirect to "Pendiente de Aprobación" (unless they are also the creator)
            // Note: Even admins need approval if they are the assignee but not the creator
            $originalRequestedStatus = $validated['status'];
            if ($validated['status'] === 'Completada' && $isAssignee && !$isCreator) {
                $validated['status'] = 'Pendiente de Aprobación';

                // Notify the creator that the task needs approval
                Notification::create([
                    'user_id' => $task->creator_id,
                    'type' => 'approval_needed',
                    'task_id' => $task->id,
                    'message' => auth()->user()->name . ' marcó como completada la tarea "' . $task->title . '" y requiere tu aprobación',
                    'is_read' => false,
                ]);
            }

            // Create system comment when status changes
            $statusMessage = $validated['status'] === 'Pendiente de Aprobación' && $originalRequestedStatus === 'Completada'
                ? "⏳ {$user->name} marcó la tarea como completada. Pendiente de aprobación del creador."
                : "📢 Estado cambiado de '{$task->status}' a '{$validated['status']}'";

            $task->comments()->create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'content' => $statusMessage,
                'is_system' => true,
            ]);
        }

        $task->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Tarea actualizada',
                'task' => $task
            ]);
        }

        return redirect()->back()->with('success', 'Tarea actualizada exitosamente');
    }

    /**
     * Approve a task that is pending approval.
     * Only the creator or admin can approve.
     */
    public function approve(Task $task)
    {
        $this->authorize('approve', $task);

        if ($task->status !== 'Pendiente de Aprobación') {
            return redirect()->back()->withErrors(['status' => 'Esta tarea no está pendiente de aprobación']);
        }

        $task->update([
            'status' => 'Completada',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Create system comment
        $task->comments()->create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'content' => '✅ Tarea aprobada por ' . auth()->user()->name,
            'is_system' => true,
        ]);

        // Notify assignee that task was approved
        if ($task->assignee_id !== auth()->id()) {
            Notification::create([
                'user_id' => $task->assignee_id,
                'type' => 'task_approved',
                'task_id' => $task->id,
                'message' => auth()->user()->name . ' aprobó tu tarea "' . $task->title . '"',
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', '¡Tarea aprobada exitosamente!');
    }

    /**
     * Reject a task that is pending approval.
     * Task goes back to "En progreso".
     */
    public function reject(Request $request, Task $task)
    {
        $this->authorize('approve', $task);

        if ($task->status !== 'Pendiente de Aprobación') {
            return redirect()->back()->withErrors(['status' => 'Esta tarea no está pendiente de aprobación']);
        }

        $reason = $request->input('reason', 'Sin motivo especificado');

        $task->update([
            'status' => 'En progreso',
        ]);

        // Create system comment
        $task->comments()->create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'content' => '❌ Tarea rechazada por ' . auth()->user()->name . '. Motivo: ' . $reason,
            'is_system' => true,
        ]);

        // Notify assignee that task was rejected
        if ($task->assignee_id !== auth()->id()) {
            Notification::create([
                'user_id' => $task->assignee_id,
                'type' => 'task_rejected',
                'task_id' => $task->id,
                'message' => auth()->user()->name . ' rechazó tu tarea "' . $task->title . '". Motivo: ' . $reason,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('info', 'Tarea devuelta a "En progreso"');
    }

    public function archive(Task $task)
    {
        $this->authorize('delete', $task); // Use delete policy for archiving as it's a destructive action from a user perspective
        $task->update(['archived_at' => Carbon::now()]); // Use archived_at timestamp

        return redirect()->route('tasks.index')->with('success', 'Tarea archivada con éxito');
    }

    public function unarchive(Task $task)
    {
        $this->authorize('update', $task); // Use update policy for unarchiving
        $task->update(['archived_at' => null]); // Set archived_at to null

        return redirect()->route('tasks.index')->with('success', 'Tarea desarchivada con éxito');
    }

    public function getComments(Task $task)
    {
        $this->authorize('view', $task);

        $comments = $task->comments()
            ->with([
                'user:id,name',
                'mentionedUsers:id,name',
                'attachments:id,comment_id,file_name,file_path,file_type,mime_type,file_size'
            ])
            ->select('comments.*')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title
            ],
            'comments' => $comments
        ]);
    }
}
