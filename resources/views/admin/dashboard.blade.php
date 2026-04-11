@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header with Quick Actions -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Panel de Administración</h1>
                    <div class="flex items-center gap-4">
                        <span id="last-update" class="text-sm text-gray-500 transition-colors"></span>
                        <button id="refresh-btn"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i>
                            <span>Actualizar</span>
                        </button>
                    </div>
                </div>

                <!-- Quick Action Buttons - Moved to top -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <a href="{{ route('admin.users') }}"
                        class="flex items-center justify-center gap-3 px-6 py-4 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Gestionar Usuarios</p>
                            <p class="text-sm text-gray-600">Administrar equipo</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.tasks') }}"
                        class="flex items-center justify-center gap-3 px-6 py-4 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-tasks text-blue-600 text-xl"></i>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Ver Todas las Tareas</p>
                            <p class="text-sm text-gray-600">Supervisar proyectos</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports') }}"
                        class="flex items-center justify-center gap-3 px-6 py-4 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-chart-bar text-blue-600 text-xl"></i>
                        <div class="text-left">
                            <p class="font-semibold text-gray-900">Reportes Avanzados</p>
                            <p class="text-sm text-gray-600">Analíticas y métricas</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Usuarios Activos</p>
                            <p id="total-users" class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUsers }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total en el sistema</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Tasks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Total de Tareas</p>
                            <p id="total-tasks" class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTasks }}</p>
                            <p class="text-xs text-gray-500 mt-1">En el sistema</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Tasa de Finalización</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2"><span
                                    id="completion-rate">{{ $completionRate }}</span>%</p>
                            <p class="text-xs text-gray-500 mt-1">Tareas completadas</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Tareas Completadas</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                <span
                                    id="completed-tasks">{{ $totalTasks > 0 ? floor($totalTasks * $completionRate / 100) : 0 }}</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Del total</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-amber-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Tasks by Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tareas por Estado</h3>
                    <div style="height: 250px; max-height: 250px; position: relative;">
                        <canvas id="tasksByStatusChart"></canvas>
                    </div>
                </div>

                <!-- Tasks by Priority -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tareas por Prioridad</h3>
                    <div style="height: 250px; max-height: 250px; position: relative;">
                        <canvas id="tasksByPriorityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Critical Tasks -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Overdue Tasks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        Tareas Vencidas
                    </h3>
                    @if($overdueTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach ($overdueTasks as $task)
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="block p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    <p class="font-semibold text-gray-900">{{ $task->title }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-user text-gray-400 mr-1"></i>
                                            {{ $task->assignee->name }}
                                        </span>
                                        <span class="text-red-600 font-semibold">
                                            <i class="fas fa-calendar-times text-red-500 mr-1"></i>
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i><br>
                            No hay tareas vencidas
                        </p>
                    @endif
                </div>

                <!-- Urgent Tasks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-fire text-amber-600"></i>
                        Tareas Urgentes
                    </h3>
                    @if($urgentTasks->count() > 0)
                        <div class="space-y-3">
                            @foreach ($urgentTasks as $task)
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="block p-4 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                    <p class="font-semibold text-gray-900">{{ $task->title }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-user text-gray-400 mr-1"></i>
                                            {{ $task->assignee->name }}
                                        </span>
                                        <span class="text-amber-600 font-semibold">
                                            <i class="fas fa-clock text-amber-500 mr-1"></i>
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i><br>
                            No hay tareas urgentes
                        </p>
                    @endif
                </div>
            </div>

            <!-- User Statistics Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Estadísticas por Usuario</h3>
                        <!-- Search Filter -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="user-search" placeholder="Buscar por nombre..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64 text-sm">
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="user-stats-table">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Creadas
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Asignadas
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Completadas
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    % Completadas
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Comentarios
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="user-stats-body">
                            @foreach ($userStats as $index => $stat)
                                @php
                                    $rate = $stat['tasks_assigned'] > 0 ? round(($stat['tasks_completed'] / $stat['tasks_assigned']) * 100, 2) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors user-stat-row"
                                    data-user-name="{{ strtolower($stat['user']->name) }}" data-index="{{ $index }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-semibold text-sm">
                                                {{ substr($stat['user']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $stat['user']->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $stat['user']->department }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $stat['tasks_created'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $stat['tasks_assigned'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $stat['tasks_completed'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[100px]">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $rate }}%"></div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">{{ $rate }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ round($stat['avg_comments'], 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Mostrando <span id="showing-start">1</span> - <span id="showing-end">10</span> de <span
                            id="total-filtered">{{ count($userStats) }}</span> usuarios
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="prev-page"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                            <i class="fas fa-chevron-left text-xs"></i>
                            Anterior
                        </button>
                        <span class="px-4 py-2 text-sm text-gray-600">
                            Página <span id="current-page">1</span> de <span id="total-pages">1</span>
                        </span>
                        <button id="next-page"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2">
                            Siguiente
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Store chart instances globally
            let statusChart, priorityChart;

            // Initialize Charts
            function initCharts(statusData, priorityData) {
                // Tasks by Status Chart
                const statusCtx = document.getElementById('tasksByStatusChart').getContext('2d');
                statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: ['#E5E7EB', '#3B82F6', '#10B981'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Tasks by Priority Chart
                const priorityCtx = document.getElementById('tasksByPriorityChart').getContext('2d');
                priorityChart = new Chart(priorityCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(priorityData),
                        datasets: [{
                            data: Object.values(priorityData),
                            backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                            borderWidth: 0,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 750,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 6
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#F3F4F6'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Update Charts with new data
            function updateCharts(statusData, priorityData) {
                if (statusChart) {
                    statusChart.data.labels = Object.keys(statusData);
                    statusChart.data.datasets[0].data = Object.values(statusData);
                    statusChart.update('active');
                }

                if (priorityChart) {
                    priorityChart.data.labels = Object.keys(priorityData);
                    priorityChart.data.datasets[0].data = Object.values(priorityData);
                    priorityChart.update('active');
                }
            }

            // Update KPI Cards
            function updateKPIs(data) {
                // Update counts with animation
                animateValue('total-users', parseInt(document.getElementById('total-users').innerText), data.totalUsers, 500);
                animateValue('total-tasks', parseInt(document.getElementById('total-tasks').innerText), data.totalTasks, 500);
                animateValue('completion-rate', parseFloat(document.getElementById('completion-rate').innerText), data.completionRate, 500);
                animateValue('completed-tasks', parseInt(document.getElementById('completed-tasks').innerText), data.completedTasks, 500);
            }

            // Animate number changes
            function animateValue(id, start, end, duration) {
                const element = document.getElementById(id);
                if (!element || start === end) return;

                const range = end - start;
                const startTime = performance.now();
                const isDecimal = id === 'completion-rate';

                function updateNumber(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const current = start + (range * progress);

                    element.textContent = isDecimal ? current.toFixed(2) : Math.floor(current);

                    if (progress < 1) {
                        requestAnimationFrame(updateNumber);
                    }
                }

                requestAnimationFrame(updateNumber);
            }

            // Fetch fresh data
            function refreshDashboard() {
                console.log('🔄 Refreshing dashboard...', new Date().toLocaleTimeString());

                fetch('{{ route("admin.analytics-data") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('✅ Dashboard updated', data);

                        // Update KPIs if data includes them
                        if (data.kpis) {
                            updateKPIs(data.kpis);
                        }

                        // Update charts if data includes them
                        if (data.tasksByStatus && data.tasksByPriority) {
                            updateCharts(data.tasksByStatus, data.tasksByPriority);
                        }

                        // Update last refresh indicator
                        const indicator = document.getElementById('last-update');
                        if (indicator) {
                            indicator.textContent = 'Actualizado: ' + new Date().toLocaleTimeString();
                            indicator.classList.add('text-green-600');
                            setTimeout(() => indicator.classList.remove('text-green-600'), 2000);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error refreshing dashboard:', error);
                    });
            }

            // Initialize with server data
            const initialStatusData = @json($tasksByStatus);
            const initialPriorityData = @json($tasksByPriority);
            initCharts(initialStatusData, initialPriorityData);

            // Auto-refresh every 30 seconds
            const refreshInterval = setInterval(refreshDashboard, 30000);

            // Manual refresh button
            const refreshBtn = document.getElementById('refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function () {
                    this.classList.add('animate-spin');
                    refreshDashboard();
                    setTimeout(() => this.classList.remove('animate-spin'), 1000);
                });
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', function () {
                clearInterval(refreshInterval);
                if (statusChart) statusChart.destroy();
                if (priorityChart) priorityChart.destroy();
            });

            console.log('📊 Dashboard real-time updates enabled');

            // ========================================
            // User Statistics Table Search & Pagination
            // ========================================
            const ITEMS_PER_PAGE = 10;
            let currentPage = 1;
            let allRows = [];
            let filteredRows = [];

            function initUserStatsTable() {
                allRows = Array.from(document.querySelectorAll('.user-stat-row'));
                filteredRows = [...allRows];
                updatePagination();
            }

            function filterUsers(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                
                if (term === '') {
                    filteredRows = [...allRows];
                } else {
                    filteredRows = allRows.filter(row => {
                        const userName = row.getAttribute('data-user-name') || '';
                        return userName.includes(term);
                    });
                }
                
                currentPage = 1;
                updatePagination();
            }

            function updatePagination() {
                const totalItems = filteredRows.length;
                const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
                const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, totalItems);

                // Hide all rows first
                allRows.forEach(row => {
                    row.style.display = 'none';
                });

                // Show only the filtered rows for current page
                for (let i = startIndex; i < endIndex; i++) {
                    if (filteredRows[i]) {
                        filteredRows[i].style.display = '';
                    }
                }

                // Update pagination info
                document.getElementById('showing-start').textContent = totalItems > 0 ? startIndex + 1 : 0;
                document.getElementById('showing-end').textContent = endIndex;
                document.getElementById('total-filtered').textContent = totalItems;
                document.getElementById('current-page').textContent = currentPage;
                document.getElementById('total-pages').textContent = totalPages;

                // Update button states
                const prevBtn = document.getElementById('prev-page');
                const nextBtn = document.getElementById('next-page');
                
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
            }

            // Search input handler
            const searchInput = document.getElementById('user-search');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filterUsers(this.value);
                    }, 300);
                });
            }

            // Pagination buttons
            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        updatePagination();
                    }
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const totalPages = Math.ceil(filteredRows.length / ITEMS_PER_PAGE) || 1;
                    if (currentPage < totalPages) {
                        currentPage++;
                        updatePagination();
                    }
                });
            }

            // Initialize the table
            initUserStatsTable();
        });
    </script>
@endsection