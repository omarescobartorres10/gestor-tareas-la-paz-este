@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
                <p class="text-gray-600 mt-1">Información personal y estadísticas</p>
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ substr($user->name, 0, 1) }}
                    </div>

                    <!-- User Details -->
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600 mt-1">{{ $user->position }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ $user->department }}</p>

                        <div class="flex items-center gap-4 mt-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $user->email }}
                            </span>

                            @if($user->role === 'admin')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    <i class="fas fa-crown mr-2"></i>
                                    Administrador
                                </span>
                            @endif

                            @if($user->can_assign_tasks)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Puede asignar tareas
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Tasks Created -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Tareas Creadas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['tasks_created'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Tasks Assigned -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Tareas Asignadas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['tasks_assigned'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center">
                            <i class="fas fa-tasks text-amber-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Tasks Completed -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Completadas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['tasks_completed'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Tasa de Completado</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['completion_rate'] }}%</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Calendar (GitHub style) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                    Calendario de Actividad
                </h3>
                <p class="text-sm text-gray-500 mb-4">Días con tareas asignadas o completadas en los últimos 6 meses</p>

                <div class="overflow-x-auto">
                    <div id="activity-calendar" class="flex flex-col gap-1">
                        <!-- Calendar will be generated by JavaScript -->
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-end gap-2 mt-4 text-sm text-gray-500">
                    <span>Menos</span>
                    <div class="w-4 h-4 rounded-sm bg-gray-100 border border-gray-200"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-300"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-400"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-600"></div>
                    <span>Más</span>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history text-gray-600 mr-2"></i>
                    Actividad Reciente
                </h3>

                <div class="space-y-3">
                    @forelse($recentTasks as $task)
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="font-semibold text-gray-900 hover:text-blue-600">
                                    {{ $task->title }}
                                </a>
                                <div class="flex items-center gap-3 mt-1 text-sm text-gray-600">
                                    <span>
                                        @if($task->creator_id === $user->id)
                                            <i class="fas fa-plus-circle text-blue-600"></i> Creada por ti
                                        @else
                                            <i class="fas fa-user text-green-600"></i> Asignada a ti
                                        @endif
                                    </span>
                                    <span>•</span>
                                    <span>{{ $task->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold
                                                @if($task->status === 'Completada') bg-green-50 text-green-700 border border-green-200
                                                @elseif($task->status === 'En progreso') bg-blue-50 text-blue-700 border border-blue-200
                                                @else bg-gray-50 text-gray-700 border border-gray-200
                                                @endif">
                                    {{ $task->status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay actividad reciente</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        // Activity data from server
        const activityData = @json($activityCalendar);

        // Format date in Spanish
        function formatDateSpanish(dateStr) {
            const date = new Date(dateStr + 'T00:00:00');
            const day = date.getDate();
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                           'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            return `${day} de ${month} de ${year}`;
        }

        // Generate calendar
        function generateActivityCalendar() {
            const container = document.getElementById('activity-calendar');
            if (!container) return;
            
            const today = new Date();
            const sixMonthsAgo = new Date(today);
            sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);

            // Find max activity for color scaling
            const maxActivity = Math.max(...Object.values(activityData), 1);

            // Create weeks container
            const weeksContainer = document.createElement('div');
            weeksContainer.className = 'flex gap-1';

            // Month labels
            const monthLabels = document.createElement('div');
            monthLabels.className = 'flex gap-1 mb-2 ml-10 text-sm text-gray-500';

            let currentMonth = sixMonthsAgo.getMonth();
            let monthsHtml = '';

            // Generate all days
            let currentDate = new Date(sixMonthsAgo);
            // Start from Sunday of that week
            currentDate.setDate(currentDate.getDate() - currentDate.getDay());

            let weekHtml = '';
            let weekCount = 0;

            while (currentDate <= today) {
                const dateStr = currentDate.toISOString().split('T')[0];
                const dayOfWeek = currentDate.getDay();
                const activity = activityData[dateStr] || 0;

                // Color based on activity level
                let colorClass = 'bg-gray-100 border border-gray-200';
                if (activity > 0) {
                    const intensity = activity / maxActivity;
                    if (intensity <= 0.25) colorClass = 'bg-green-300';
                    else if (intensity <= 0.5) colorClass = 'bg-green-400';
                    else if (intensity <= 0.75) colorClass = 'bg-green-500';
                    else colorClass = 'bg-green-600';
                }

                // Start new week column
                if (dayOfWeek === 0 && weekHtml) {
                    const weekDiv = document.createElement('div');
                    weekDiv.className = 'flex flex-col gap-1';
                    weekDiv.innerHTML = weekHtml;
                    weeksContainer.appendChild(weekDiv);
                    weekHtml = '';
                    weekCount++;

                    // Add month label when month changes
                    if (currentDate.getMonth() !== currentMonth) {
                        const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                        monthsHtml += `<span style="width: ${weekCount * 17}px">${monthNames[currentDate.getMonth()]}</span>`;
                        currentMonth = currentDate.getMonth();
                        weekCount = 0;
                    }
                }

                // Spanish tooltip format like GitHub
                const formattedDate = formatDateSpanish(dateStr);
                let tooltip;
                if (activity === 0) {
                    tooltip = `Sin contribuciones el ${formattedDate}`;
                } else if (activity === 1) {
                    tooltip = `1 contribución el ${formattedDate}`;
                } else {
                    tooltip = `${activity} contribuciones el ${formattedDate}`;
                }

                weekHtml += `<div class="w-4 h-4 rounded-sm ${colorClass} cursor-pointer hover:ring-1 hover:ring-gray-400" title="${tooltip}"></div>`;

                currentDate.setDate(currentDate.getDate() + 1);
            }

            // Add remaining week
            if (weekHtml) {
                const weekDiv = document.createElement('div');
                weekDiv.className = 'flex flex-col gap-1';
                weekDiv.innerHTML = weekHtml;
                weeksContainer.appendChild(weekDiv);
            }

            // Day labels (larger)
            const dayLabels = document.createElement('div');
            dayLabels.className = 'flex flex-col gap-1 mr-2 text-sm text-gray-500';
            dayLabels.innerHTML = `
                    <div class="h-4"></div>
                    <div class="h-4 flex items-center">Lun</div>
                    <div class="h-4"></div>
                    <div class="h-4 flex items-center">Mié</div>
                    <div class="h-4"></div>
                    <div class="h-4 flex items-center">Vie</div>
                    <div class="h-4"></div>
                `;

            // Assemble calendar
            monthLabels.innerHTML = monthsHtml;
            container.innerHTML = '';
            container.appendChild(monthLabels);

            const calendarRow = document.createElement('div');
            calendarRow.className = 'flex';
            calendarRow.appendChild(dayLabels);
            calendarRow.appendChild(weeksContainer);
            container.appendChild(calendarRow);
        }

        document.addEventListener('DOMContentLoaded', generateActivityCalendar);
    </script>
@endsection