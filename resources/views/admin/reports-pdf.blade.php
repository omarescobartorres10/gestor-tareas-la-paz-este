<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Tareas - Gestor de Tareas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #1e293b;
            line-height: 1.6;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            color: #1e40af;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .header .subtitle {
            color: #64748b;
            font-size: 13px;
        }

        .header .generated {
            color: #94a3b8;
            font-size: 11px;
            margin-top: 4px;
        }

        .section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .section-title {
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            border-left: 3px solid #3b82f6;
        }

        .stat-card.blue {
            border-left-color: #3b82f6;
        }

        .stat-card.purple {
            border-left-color: #8b5cf6;
        }

        .stat-card.green {
            border-left-color: #22c55e;
        }

        .stat-card.yellow {
            border-left-color: #eab308;
        }

        .stat-card.red {
            border-left-color: #ef4444;
        }

        .stat-value {
            font-size: 22px;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .time-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .time-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .time-card.warning {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }

        .time-card.success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        }

        .time-card.info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .time-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
        }

        .time-card .label {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .chart-section {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .chart-title {
            font-size: 13px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 10px;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f1f5f9;
            font-weight: 600;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        td {
            color: #334155;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
            border-radius: 3px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 10px;
        }

        /* Print Styles */
        @media print {
            @page {
                margin: 1.5cm;
                size: A4;
            }

            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }

            .section {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .chart-section {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .charts-grid {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Stack charts vertically on print for better fit */
            .charts-grid:last-child {
                page-break-before: always;
                margin-top: 20px;
            }

            .chart-container {
                height: 180px;
            }

            /* Hide URLs completely */
            a[href]:after {
                content: none !important;
            }

            /* Force colors on print */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
</head>

<body>
    <div class="header">
        <h1>📊 Reporte de Tareas</h1>
        <div class="subtitle">Alcaldía de la Paz Este - Sistema de Gestión de Tareas</div>
        <div class="generated">Generado el: {{ $generatedAt }}</div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="section">
        <h2 class="section-title">📈 Estadísticas Generales</h2>
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-value">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Usuarios Activos</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                <div class="stat-label">Total Tareas</div>
            </div>
            <div class="stat-card green">
                <div class="stat-value">{{ $stats['completed_tasks'] }}</div>
                <div class="stat-label">Completadas</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-value">{{ $stats['in_progress_tasks'] }}</div>
                <div class="stat-label">En Progreso</div>
            </div>
            <div class="stat-card red">
                <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
        </div>
    </div>

    <!-- Análisis de Tiempos -->
    <div class="section">
        <h2 class="section-title">⏱️ Análisis de Tiempos</h2>
        <div class="time-cards">
            <div class="time-card warning">
                <div class="value">{{ $timeAnalysis['overdue_tasks'] }}</div>
                <div class="label">Tareas Vencidas</div>
            </div>
            <div class="time-card">
                <div class="value">{{ $timeAnalysis['due_soon_tasks'] }}</div>
                <div class="label">Vencen Pronto</div>
            </div>
            <div class="time-card info">
                <div class="value">{{ $timeAnalysis['avg_completion_days'] }} días</div>
                <div class="label">Tiempo Promedio</div>
            </div>
            <div class="time-card success">
                <div class="value">{{ $timeAnalysis['on_time_rate'] }}%</div>
                <div class="label">Tasa de Cumplimiento</div>
            </div>
        </div>
    </div>

    <!-- Gráficos Visuales - Parte 1 -->
    <div class="section">
        <h2 class="section-title">📊 Análisis Visual - Distribución</h2>
        <div class="charts-grid">
            <div class="chart-section">
                <div class="chart-title">Distribución por Estado</div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="chart-section">
                <div class="chart-title">Distribución por Prioridad</div>
                <div class="chart-container">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Visuales - Parte 2 -->
    <div class="section">
        <h2 class="section-title">📊 Análisis Visual - Rendimiento</h2>
        <div class="charts-grid">
            <div class="chart-section">
                <div class="chart-title">Top 10 Productividad por Usuario</div>
                <div class="chart-container">
                    <canvas id="productivityChart"></canvas>
                </div>
            </div>

            <div class="chart-section">
                <div class="chart-title">Tendencia Últimos 7 Días</div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Productividad por Usuario -->
    <div class="section">
        <h2 class="section-title">👥 Productividad por Usuario (Top 15)</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Asignadas</th>
                    <th>Completadas</th>
                    <th>Tasa</th>
                    <th>Progreso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userProductivity->take(15) as $user)
                    <tr>
                        <td><strong>{{ $user['name'] }}</strong></td>
                        <td>{{ $user['assigned'] }}</td>
                        <td>{{ $user['completed'] }}</td>
                        <td>{{ $user['rate'] }}%</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $user['rate'] }}%"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Estadísticas por Departamento -->
    @if($departmentStats->count() > 0)
        <div class="section">
            <h2 class="section-title">🏢 Estadísticas por Departamento</h2>
            <table>
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th>Usuarios</th>
                        <th>Tareas Asignadas</th>
                        <th>Completadas</th>
                        <th>Tasa de Completado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departmentStats as $dept)
                        <tr>
                            <td><strong>{{ $dept['department'] }}</strong></td>
                            <td>{{ $dept['users'] }}</td>
                            <td>{{ $dept['tasks_assigned'] }}</td>
                            <td>{{ $dept['tasks_completed'] }}</td>
                            <td>{{ $dept['completion_rate'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif>

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Gestión de Tareas</p>
        <p>© {{ date('Y') }} Alcaldía de la Paz Este</p>
    </div>

    <script>
        // Register datalabels plugin globally
        Chart.register(ChartDataLabels);

        // Chart.js configuration
        Chart.defaults.font.family = 'Segoe UI, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.plugins.legend.labels.boxWidth = 12;
        Chart.defaults.plugins.legend.labels.font = { size: 10 };

        const colors = {
            primary: 'rgb(59, 130, 246)',
            success: 'rgb(34, 197, 94)',
            warning: 'rgb(234, 179, 8)',
            danger: 'rgb(239, 68, 68)',
            purple: 'rgb(139, 92, 246)',
        };

        // Status Distribution Chart
        @php
            $tasksByStatus = \App\Models\Task::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        @endphp

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($tasksByStatus)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($tasksByStatus)) !!},
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
                        position: 'bottom',
                        labels: { padding: 8, font: { size: 10 } }
                    },
                    datalabels: {
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(0);
                            return value + '\n(' + percentage + '%)';
                        },
                        color: '#fff',
                        font: { weight: 'bold', size: 10 },
                        textAlign: 'center',
                        anchor: 'center',
                        align: 'center'
                    }
                }
            }
        });

        // Priority Distribution Chart
        @php
            $tasksByPriority = \App\Models\Task::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();
        @endphp

        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        new Chart(priorityCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($tasksByPriority)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($tasksByPriority)) !!},
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
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
                        labels: { padding: 8, font: { size: 10 } }
                    },
                    datalabels: {
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(0);
                            return value + '\n(' + percentage + '%)';
                        },
                        color: '#fff',
                        font: { weight: 'bold', size: 10 },
                        textAlign: 'center',
                        anchor: 'center',
                        align: 'center'
                    }
                }
            }
        });

        // User Productivity Chart (Top 10)
        const productivityCtx = document.getElementById('productivityChart').getContext('2d');
        const userProductivity = @json($userProductivity->take(10));

        new Chart(productivityCtx, {
            type: 'bar',
            data: {
                labels: userProductivity.map(u => u.name.length > 12 ? u.name.substring(0, 12) + '...' : u.name),
                datasets: [
                    {
                        label: 'Asignadas',
                        data: userProductivity.map(u => u.assigned),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: colors.primary,
                        borderWidth: 1,
                    },
                    {
                        label: 'Completadas',
                        data: userProductivity.map(u => u.completed),
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
                        labels: { padding: 6, font: { size: 9 } }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#1e293b',
                        font: { weight: 'bold', size: 9 },
                        formatter: (value) => value > 0 ? value : ''
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 9 } }
                    },
                    y: {
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });

        // Task Trend Chart (Last 7 days)
        @php
            $trendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                $trendData[] = [
                    'date' => $date->format('d/m'),
                    'created' => \App\Models\Task::whereDate('created_at', $date->format('Y-m-d'))->count(),
                    'completed' => \App\Models\Task::whereDate('updated_at', $date->format('Y-m-d'))
                        ->where('status', 'Completada')
                        ->count(),
                ];
            }
        @endphp

        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = @json($trendData);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.date),
                datasets: [
                    {
                        label: 'Creadas',
                        data: trendData.map(d => d.created),
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: colors.primary,
                    },
                    {
                        label: 'Completadas',
                        data: trendData.map(d => d.completed),
                        borderColor: colors.success,
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: colors.success,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { padding: 6, font: { size: 9 } }
                    },
                    datalabels: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 9 } }
                    },
                    x: {
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });

        // Wait for charts to render before allowing print/save
        setTimeout(() => {
            document.body.style.opacity = '1';
            // Auto-trigger print dialog after charts render
            // window.print();
        }, 1500);
    </script>
</body>

</html>