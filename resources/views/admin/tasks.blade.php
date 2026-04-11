@extends('layouts.app')

@section('title', 'Todas las Tareas - Admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-tasks text-blue-600"></i> Todas las Tareas del Sistema
            </h2>
            <p class="text-gray-600 mt-2">Vista completa de todas las tareas con filtros avanzados</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('admin.tasks') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-search"></i> Búsqueda
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Título o descripción..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Creator Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-plus"></i> Creador
                        </label>
                        <select name="creator_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('creator_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assignee Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-check"></i> Asignado a
                        </label>
                        <select name="assignee_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('assignee_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-info-circle"></i> Estado
                        </label>
                        <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="En progreso" {{ request('status') == 'En progreso' ? 'selected' : '' }}>En progreso
                            </option>
                            <option value="Pendiente de Aprobación" {{ request('status') == 'Pendiente de Aprobación' ? 'selected' : '' }}>Pendiente de Aprobación</option>
                            <option value="Completada" {{ request('status') == 'Completada' ? 'selected' : '' }}>Completada
                            </option>
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-exclamation"></i> Prioridad
                        </label>
                        <select name="priority"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todas</option>
                            <option value="Alta" {{ request('priority') == 'Alta' ? 'selected' : '' }}>Alta</option>
                            <option value="Media" {{ request('priority') == 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Baja" {{ request('priority') == 'Baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt"></i> Desde
                        </label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-check"></i> Hasta
                        </label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Archived Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-archive"></i> Archivadas
                        </label>
                        <select name="archived"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="0" {{ request('archived') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ request('archived') == '1' ? 'selected' : '' }}>Sí</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 justify-end">
                    <a href="{{ route('admin.tasks') }}"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tasks Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold">Título</th>
                            <th class="px-6 py-4 text-left font-bold">Creador</th>
                            <th class="px-6 py-4 text-left font-bold">Asignado a</th>
                            <th class="px-6 py-4 text-left font-bold">Estado</th>
                            <th class="px-6 py-4 text-left font-bold">Prioridad</th>
                            <th class="px-6 py-4 text-left font-bold">Fecha Límite</th>
                            <th class="px-6 py-4 text-left font-bold">Comentarios</th>
                            <th class="px-6 py-4 text-center font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr
                                class="hover:bg-gray-50 transition {{ $task->isOverdue() ? 'bg-red-50' : ($task->isDueSoon() ? 'bg-yellow-50' : '') }}">
                                <td class="px-6 py-4">
                                    <a href="{{ route('tasks.show', $task) }}"
                                        class="font-semibold text-gray-800 hover:text-blue-600 transition">
                                        {{ Str::limit($task->title, 40) }}
                                    </a>
                                    @if($task->isOverdue())
                                        <span class="ml-2 text-xs bg-red-500 text-white px-2 py-1 rounded-full">VENCIDA</span>
                                    @elseif($task->isDueSoon())
                                        <span class="ml-2 text-xs bg-yellow-500 text-white px-2 py-1 rounded-full">URGENTE</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($task->creator->name, 0, 1) }}
                                        </div>
                                        {{ $task->creator->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-r from-green-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ substr($task->assignee->name, 0, 1) }}
                                        </div>
                                        {{ $task->assignee->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($task->status === 'Completada') bg-green-100 text-green-800
                                                    @elseif($task->status === 'En progreso') bg-blue-100 text-blue-800
                                                    @elseif($task->status === 'Pendiente de Aprobación') bg-amber-100 text-amber-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                        @if($task->status === 'Pendiente de Aprobación')⏰ @endif{{ $task->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                    @if($task->priority === 'Alta') bg-red-100 text-red-800
                                                    @elseif($task->priority === 'Media') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                    {{ $task->due_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">
                                        {{ $task->comments->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- View -->
                                        <a href="{{ route('tasks.show', $task) }}"
                                            class="text-blue-600 hover:text-blue-800 transition" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Reassign -->
                                        <button
                                            onclick="openReassignModal({{ $task->id }}, '{{ $task->title }}', {{ $task->assignee_id }})"
                                            class="text-green-600 hover:text-green-800 transition" title="Reasignar">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>

                                        <!-- Archive/Unarchive -->
                                        @if(!$task->archived_at)
                                            <form method="POST" action="{{ route('tasks.archive', $task) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('¿Archivar tarea?')"
                                                    class="text-yellow-600 hover:text-yellow-800 transition" title="Archivar">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('tasks.unarchive', $task) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('¿Desarchivar tarea?')"
                                                    class="text-green-600 hover:text-green-800 transition" title="Desarchivar">
                                                    <i class="fas fa-box-open"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-semibold">No se encontraron tareas</p>
                                    <p class="text-sm">Prueba ajustando los filtros</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reassign Modal -->
    <div id="reassignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-exchange-alt text-green-600"></i> Reasignar Tarea
                </h3>
                <button onclick="closeReassignModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="reassignForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tarea</label>
                    <p id="reassignTaskTitle" class="text-gray-600 p-3 bg-gray-50 rounded"></p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Asignar a</label>
                    <select name="assignee_id" id="reassignAssignee"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->position }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeReassignModal()"
                        class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition font-semibold">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-semibold">
                        <i class="fas fa-check"></i> Reasignar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openReassignModal(taskId, taskTitle, currentAssigneeId) {
            document.getElementById('reassignTaskTitle').textContent = taskTitle;
            document.getElementById('reassignAssignee').value = currentAssigneeId;
            document.getElementById('reassignForm').action = `/admin/tasks/${taskId}/reassign`;
            document.getElementById('reassignModal').classList.remove('hidden');
        }

        function closeReassignModal() {
            document.getElementById('reassignModal').classList.add('hidden');
        }

        // Close modal on background click
        document.getElementById('reassignModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeReassignModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !document.getElementById('reassignModal')?.classList.contains('hidden')) {
                closeReassignModal();
            }
        });
    </script>
@endsection