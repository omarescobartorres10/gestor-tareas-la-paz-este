@extends('layouts.app')

@section('title', 'Reportes y Analíticas - Admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-chart-bar text-green-600"></i> Reportes y Analíticas
                </h2>
                <p class="text-gray-600 mt-2">Análisis detallado del rendimiento del sistema</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.export-pdf') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg inline-flex items-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
                <a href="{{ route('admin.export-csv') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg inline-flex items-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-file-csv"></i> Exportar CSV
                </a>
            </div>
        </div>

        <!-- Date Filters -->
        <div class="bg-white rounded-lg shadow-md p-5 mb-6">
            <form method="GET" action="{{ route('admin.reports') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500"></i> Periodo Rápido
                    </label>
                    <select id="quickPeriod"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Personalizado</option>
                        <option value="7">Últimos 7 días</option>
                        <option value="30" selected>Últimos 30 días</option>
                        <option value="90">Últimos 90 días</option>
                        <option value="365">Último año</option>
                        <option value="all">Todo el tiempo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-green-500"></i> Fecha Inicio
                    </label>
                    <input type="date" name="start_date" id="startDate" value="{{ $startDate }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check text-purple-500"></i> Fecha Fin
                    </label>
                    <input type="date" name="end_date" id="endDate" value="{{ $endDate }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center justify-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-filter"></i> Aplicar Filtro
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.reports') }}"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg inline-flex items-center justify-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-redo"></i> Restablecer
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600 font-semibold">Total Usuarios</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_users'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
                <p class="text-sm text-gray-600 font-semibold">Total Tareas</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_tasks'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                <p class="text-sm text-gray-600 font-semibold">Completadas</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['completed_tasks'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-600 font-semibold">En Progreso</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['in_progress_tasks'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <p class="text-sm text-gray-600 font-semibold">Pendientes</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['pending_tasks'] }}</p>
            </div>
        </div>

        <!-- Time Analysis Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold mb-6 text-gray-800">
                <i class="fas fa-clock text-purple-600"></i> Análisis de Tiempos
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-600 font-semibold">Tareas Vencidas</p>
                            <p class="text-4xl font-bold text-red-700 mt-2">{{ $timeAnalysis['overdue_tasks'] }}</p>
                        </div>
                        <div class="bg-red-200 rounded-full p-3">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5 border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-600 font-semibold">Vencen Pronto</p>
                            <p class="text-4xl font-bold text-yellow-700 mt-2">{{ $timeAnalysis['due_soon_tasks'] }}</p>
                            <p class="text-xs text-yellow-500 mt-1">Próximos 3 días</p>
                        </div>
                        <div class="bg-yellow-200 rounded-full p-3">
                            <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 font-semibold">Tiempo Promedio</p>
                            <p class="text-4xl font-bold text-blue-700 mt-2">{{ $timeAnalysis['avg_completion_days'] }}</p>
                            <p class="text-xs text-blue-500 mt-1">días para completar</p>
                        </div>
                        <div class="bg-blue-200 rounded-full p-3">
                            <i class="fas fa-stopwatch text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-600 font-semibold">Tasa de Cumplimiento</p>
                            <p class="text-4xl font-bold text-green-700 mt-2">{{ $timeAnalysis['on_time_rate'] }}%</p>
                            <p class="text-xs text-green-500 mt-1">completadas a tiempo</p>
                        </div>
                        <div class="bg-green-200 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tasks Trend Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-chart-line text-blue-600"></i> Tendencia de Tareas (14 días)
                </h3>
                <div class="h-64">
                    <canvas id="tasksTrendChart"></canvas>
                </div>
            </div>

            <!-- Tasks by Status Pie Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-chart-pie text-purple-600"></i> Distribución por Estado
                </h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User Productivity Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-users text-blue-600"></i> Productividad por Usuario
                </h3>
                <div class="h-80">
                    <canvas id="userProductivityChart"></canvas>
                </div>
            </div>

            <!-- Department Stats Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-building text-green-600"></i> Rendimiento por Departamento
                </h3>
                @if(count($departmentStats) > 0)
                    <div class="h-80">
                        <canvas id="departmentChart"></canvas>
                    </div>
                @else
                    <div class="h-80 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <i class="fas fa-building text-6xl mb-4"></i>
                            <p>No hay datos de departamentos disponibles</p>
                            <p class="text-sm">Asigna departamentos a los usuarios para ver estadísticas</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Charts Row 3 - New Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Priority Distribution Pie Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-flag text-orange-600"></i> Distribución por Prioridad
                </h3>
                <div class="h-64">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>

            <!-- Completion Rate Gauge -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-tachometer-alt text-indigo-600"></i> Tasa de Completado General
                </h3>
                <div class="h-64">
                    <canvas id="completionGaugeChart"></canvas>
                </div>
            </div>

            <!-- Task Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-calendar-check text-teal-600"></i> Cumplimiento de Plazos
                </h3>
                <div class="h-64">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 4 - Additional Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tasks by Creator -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-user-plus text-purple-600"></i> Top Creadores de Tareas
                </h3>
                <div class="h-80">
                    <canvas id="creatorsChart"></canvas>
                </div>
            </div>

            <!-- Weekly Activity Heatmap -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-fire text-red-600"></i> Actividad Semanal
                </h3>
                <div class="h-80">
                    <canvas id="weeklyActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User Productivity Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-table text-blue-600"></i> Detalle de Productividad
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuario</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asignadas</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Completadas</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tasa</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Progreso</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($userProductivity as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($user['name'], 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $user['name'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-600">
                                        {{ $user['assigned'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-600">
                                        {{ $user['completed'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full"
                                                    style="width: {{ min($user['rate'], 100) }}%"></div>
                                            </div>
                                            <span
                                                class="text-sm font-semibold {{ $user['rate'] >= 70 ? 'text-green-600' : ($user['rate'] >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $user['rate'] }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Department Stats Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 text-gray-800">
                    <i class="fas fa-sitemap text-green-600"></i> Detalle por Departamento
                </h3>
                @if(count($departmentStats) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Departamento</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuarios</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tareas</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tasa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($departmentStats as $dept)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-8 w-8 rounded-lg bg-gradient-to-r from-green-500 to-teal-500 flex items-center justify-center text-white">
                                                    <i class="fas fa-building text-sm"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $dept['department'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-600">
                                            {{ $dept['users'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap text-sm text-gray-600">
                                            {{ $dept['tasks_completed'] }}/{{ $dept['tasks_assigned'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                                                            {{ $dept['completion_rate'] >= 70 ? 'bg-green-100 text-green-800' : ($dept['completion_rate'] >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $dept['completion_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <i class="fas fa-folder-open text-6xl mb-4"></i>
                            <p>No hay departamentos configurados</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-flag text-orange-600"></i> Distribución por Prioridad
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $priorityColors = [
                        'Alta' => ['bg' => 'bg-red-100', 'border' => 'border-red-500', 'text' => 'text-red-700', 'icon' => 'text-red-500'],
                        'Media' => ['bg' => 'bg-yellow-100', 'border' => 'border-yellow-500', 'text' => 'text-yellow-700', 'icon' => 'text-yellow-500'],
                        'Baja' => ['bg' => 'bg-green-100', 'border' => 'border-green-500', 'text' => 'text-green-700', 'icon' => 'text-green-500'],
                    ];
                @endphp
                @foreach(['Alta', 'Media', 'Baja'] as $priority)
                    @php $count = $tasksByPriority[$priority] ?? 0; @endphp
                    <div
                        class="{{ $priorityColors[$priority]['bg'] }} rounded-xl p-5 border-l-4 {{ $priorityColors[$priority]['border'] }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm {{ $priorityColors[$priority]['text'] }} font-semibold">Prioridad
                                    {{ $priority }}
                                </p>
                                <p class="text-3xl font-bold {{ $priorityColors[$priority]['text'] }} mt-2">{{ $count }}</p>
                                @if($stats['total_tasks'] > 0)
                                    <p class="text-xs {{ $priorityColors[$priority]['text'] }} opacity-75 mt-1">
                                        {{ round(($count / $stats['total_tasks']) * 100, 1) }}% del total
                                    </p>
                                @endif
                            </div>
                            <i class="fas fa-flag {{ $priorityColors[$priority]['icon'] }} text-3xl opacity-50"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-center mt-8">
            <a href="{{ route('admin.dashboard') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg inline-flex items-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Wait for Chart.js to be loaded (it's deferred)
        document.addEventListener('DOMContentLoaded', function () {
            // Check if Chart is available, if not wait a bit more
            function initCharts() {
                if (typeof Chart === 'undefined') {
                    setTimeout(initCharts, 100);
                    return;
                }

                // Chart.js configuration
                Chart.defaults.font.family = 'Figtree, system-ui, sans-serif';

                // Color palettes
                const colors = {
                    primary: 'rgb(59, 130, 246)',
                    success: 'rgb(34, 197, 94)',
                    warning: 'rgb(234, 179, 8)',
                    danger: 'rgb(239, 68, 68)',
                    purple: 'rgb(139, 92, 246)',
                    teal: 'rgb(20, 184, 166)',
                };

                // Tasks Trend Chart
                const tasksTrendCtx = document.getElementById('tasksTrendChart').getContext('2d');
                new Chart(tasksTrendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(collect($tasksTrend)->pluck('date')) !!},
                        datasets: [
                            {
                                label: 'Creadas',
                                data: {!! json_encode(collect($tasksTrend)->pluck('created')) !!},
                                borderColor: colors.primary,
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Completadas',
                                data: {!! json_encode(collect($tasksTrend)->pluck('completed')) !!},
                                borderColor: colors.success,
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Status Distribution Chart
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusData = @json($tasksByStatus);
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: [
                                colors.success,
                                colors.warning,
                                colors.danger,
                                colors.purple,
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        },
                        cutout: '60%',
                    }
                });

                // User Productivity Chart
                const userProductivityCtx = document.getElementById('userProductivityChart').getContext('2d');
                const userProductivityData = @json($userProductivity);
                new Chart(userProductivityCtx, {
                    type: 'bar',
                    data: {
                        labels: userProductivityData.map(u => u.name.length > 15 ? u.name.substring(0, 15) + '...' : u.name),
                        datasets: [
                            {
                                label: 'Asignadas',
                                data: userProductivityData.map(u => u.assigned),
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: colors.primary,
                                borderWidth: 1,
                            },
                            {
                                label: 'Completadas',
                                data: userProductivityData.map(u => u.completed),
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: colors.success,
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Department Chart
                @if(count($departmentStats) > 0)
                    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
                    const departmentData = @json($departmentStats);
                    new Chart(departmentCtx, {
                        type: 'bar',
                        data: {
                            labels: departmentData.map(d => d.department),
                            datasets: [
                                {
                                    label: 'Tareas Asignadas',
                                    data: departmentData.map(d => d.tasks_assigned),
                                    backgroundColor: 'rgba(20, 184, 166, 0.7)',
                                    borderColor: colors.teal,
                                    borderWidth: 1,
                                },
                                {
                                    label: 'Completadas',
                                    data: departmentData.map(d => d.tasks_completed),
                                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                    borderColor: colors.success,
                                    borderWidth: 1,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                @endif

                // Priority Distribution Chart
                const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                const priorityData = @json($tasksByPriority);
                new Chart(priorityCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(priorityData),
                        datasets: [{
                            data: Object.values(priorityData),
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)',   // Alta - Red
                                'rgba(234, 179, 8, 0.8)',   // Media - Yellow
                                'rgba(34, 197, 94, 0.8)',   // Baja - Green
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Completion Rate Gauge Chart
                const completionRate = {{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0 }};
                const gaugeCtx = document.getElementById('completionGaugeChart').getContext('2d');
                new Chart(gaugeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'Pendientes'],
                        datasets: [{
                            data: [completionRate, 100 - completionRate],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(229, 231, 235, 0.5)',
                            ],
                            borderWidth: 0,
                            circumference: 180,
                            rotation: 270,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: false,
                            }
                        },
                        cutout: '75%',
                    },
                    plugins: [{
                        id: 'gaugeText',
                        afterDraw: (chart) => {
                            const ctx = chart.ctx;
                            ctx.save();
                            const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                            const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2 + 20;
                            ctx.font = 'bold 32px sans-serif';
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(completionRate + '%', centerX, centerY);
                            ctx.font = '14px sans-serif';
                            ctx.fillStyle = '#64748b';
                            ctx.fillText('Completado', centerX, centerY + 30);
                            ctx.restore();
                        }
                    }]
                });

                // Timeline Chart (On-time vs Late)
                const timelineCtx = document.getElementById('timelineChart').getContext('2d');
                new Chart(timelineCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Cumplimiento'],
                        datasets: [
                            {
                                label: 'A Tiempo',
                                data: [{{ $timeAnalysis['completed_on_time'] }}],
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: colors.success,
                                borderWidth: 1,
                            },
                            {
                                label: 'Tarde',
                                data: [{{ $timeAnalysis['completed_late'] }}],
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: colors.danger,
                                borderWidth: 1,
                            },
                            {
                                label: 'Vencidas',
                                data: [{{ $timeAnalysis['overdue_tasks'] }}],
                                backgroundColor: 'rgba(251, 146, 60, 0.8)',
                                borderColor: 'rgb(251, 146, 60)',
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Top Creators Chart
                const creatorsCtx = document.getElementById('creatorsChart').getContext('2d');
                const topCreators = @json(\App\Models\User::withCount('tasksCreated')
                    ->orderBy('tasks_created_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'name' => $user->name,
                            'count' => $user->tasks_created_count
                        ];
                }));

                new Chart(creatorsCtx, {
                    type: 'bar',
                    data: {
                        labels: topCreators.map(c => c.name.length > 15 ? c.name.substring(0, 15) + '...' : c.name),
                        datasets: [{
                            label: 'Tareas Creadas',
                            data: topCreators.map(c => c.count),
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderColor: colors.purple,
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false,
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Weekly Activity Chart
                const weeklyCtx = document.getElementById('weeklyActivityChart').getContext('2d');
                const last7Days = [];
                const createdLast7 = [];
                const completedLast7 = [];

                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    const dayName = date.toLocaleDateString('es-ES', { weekday: 'short' });
                    last7Days.push(dayName.charAt(0).toUpperCase() + dayName.slice(1));
                }

                @php
                    $weeklyData = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                        $weeklyData[] = [
                            'created' => \App\Models\Task::whereDate('created_at', $date)->count(),
                            'completed' => \App\Models\Task::whereDate('updated_at', $date)
                                ->where('status', 'Completada')
                                ->count(),
                        ];
                    }
                @endphp

                const weeklyData = @json($weeklyData);

                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: last7Days,
                        datasets: [
                            {
                                label: 'Creadas',
                                data: weeklyData.map(d => d.created),
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: colors.primary,
                                borderWidth: 1,
                            },
                            {
                                label: 'Completadas',
                                data: weeklyData.map(d => d.completed),
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: colors.success,
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Quick Period Selector
                document.getElementById('quickPeriod').addEventListener('change', function () {
                    const days = this.value;
                    const endDateInput = document.getElementById('endDate');
                    const startDateInput = document.getElementById('startDate');

                    const today = new Date();
                    endDateInput.valueAsDate = today;

                    if (days === 'all') {
                        // Set to a very old date for "all time"
                        const oldDate = new Date();
                        oldDate.setFullYear(oldDate.getFullYear() - 10);
                        startDateInput.valueAsDate = oldDate;
                    } else if (days !== '') {
                        const startDate = new Date();
                        startDate.setDate(startDate.getDate() - parseInt(days));
                        startDateInput.valueAsDate = startDate;
                    }
                    // If empty (Personalizado), don't change dates
                });
            }
            
            // Start chart initialization
            initCharts();
        });
    </script>
@endsection