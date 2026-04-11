<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile with statistics.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Personal statistics
        $stats = [
            'tasks_created' => Task::where('creator_id', $user->id)->count(),
            'tasks_assigned' => Task::where('assignee_id', $user->id)->count(),
            'tasks_completed' => Task::where('assignee_id', $user->id)
                ->where('status', 'Completada')
                ->count(),
            'tasks_in_progress' => Task::where('assignee_id', $user->id)
                ->where('status', 'En progreso')
                ->count(),
            'tasks_pending' => Task::where('assignee_id', $user->id)
                ->where('status', 'Pendiente')
                ->count(),
            'comments_made' => DB::table('comments')
                ->where('user_id', $user->id)
                ->count(),
        ];

        // Completion rate
        $stats['completion_rate'] = $stats['tasks_assigned'] > 0
            ? round(($stats['tasks_completed'] / $stats['tasks_assigned']) * 100, 1)
            : 0;

        // Tasks by priority (assigned to user)
        $tasksByPriority = Task::where('assignee_id', $user->id)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();

        // Recent activity (last 10 tasks)
        $recentTasks = Task::where(function ($query) use ($user) {
            $query->where('creator_id', $user->id)
                ->orWhere('assignee_id', $user->id);
        })
            ->with(['creator', 'assignee'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly activity (last 6 months)
        $monthlyActivity = Task::where('assignee_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Activity calendar data (last 6 months - for GitHub-style calendar)
        $activityCalendar = [];

        // Get dates when tasks were assigned to user
        $assignedDates = Task::where('assignee_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE(created_at) as activity_date'), DB::raw('count(*) as count'))
            ->groupBy('activity_date')
            ->get()
            ->pluck('count', 'activity_date')
            ->toArray();

        // Get dates when tasks were completed by user
        $completedDates = Task::where('assignee_id', $user->id)
            ->where('status', 'Completada')
            ->whereNotNull('updated_at')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE(updated_at) as activity_date'), DB::raw('count(*) as count'))
            ->groupBy('activity_date')
            ->get()
            ->pluck('count', 'activity_date')
            ->toArray();

        // Merge both arrays (sum counts for same dates)
        foreach ($assignedDates as $date => $count) {
            $activityCalendar[$date] = ($activityCalendar[$date] ?? 0) + $count;
        }
        foreach ($completedDates as $date => $count) {
            $activityCalendar[$date] = ($activityCalendar[$date] ?? 0) + $count;
        }

        return view('profile.edit', compact('user', 'stats', 'tasksByPriority', 'recentTasks', 'monthlyActivity', 'activityCalendar'));
    }
}
