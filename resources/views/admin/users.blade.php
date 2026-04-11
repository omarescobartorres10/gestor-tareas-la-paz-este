@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-users text-purple-600"></i> Gestión de Usuarios
                </h2>
                <p class="text-gray-600 mt-2">Administra todos los usuarios del sistema</p>
            </div>
            <a href="{{ route('admin.create-user') }}"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg inline-flex items-center gap-2 font-semibold shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-user-plus"></i> Nuevo Usuario
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p class="font-semibold"><i class="fas fa-check-circle"></i> {{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                <p class="font-semibold"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</p>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-search"></i> Búsqueda
                    </label>
                    <input type="text" id="searchUsers" placeholder="Buscar por nombre o email..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-filter"></i> Rol
                    </label>
                    <select id="filterRole"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="admin">Admin</option>
                        <option value="usuario">Usuario</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on"></i> Estado
                    </label>
                    <select id="filterStatus"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="usersTable">
                    <thead class="bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold">Usuario</th>
                            <th class="px-6 py-4 text-left font-bold">Email</th>
                            <th class="px-6 py-4 text-left font-bold">Rol</th>
                            <th class="px-6 py-4 text-left font-bold">Departamento</th>
                            <th class="px-6 py-4 text-left font-bold">Estado</th>
                            <th class="px-6 py-4 text-left font-bold">Estadísticas</th>
                            <th class="px-6 py-4 text-center font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition user-row" data-name="{{ strtolower($user->name) }}"
                                data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role }}"
                                data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                            @if($user->employee_id)
                                                <p class="text-xs text-blue-600 font-medium"><i class="fas fa-id-card"></i> CI:
                                                    {{ $user->employee_id }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-gray-500">{{ $user->position }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <i class="fas fa-envelope text-gray-400"></i> {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas {{ $user->role === 'admin' ? 'fa-shield-alt' : 'fa-user' }} mr-1"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $user->department ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-circle text-[8px] mr-1"></i>
                                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 text-xs">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded" title="Tareas creadas">
                                            <i class="fas fa-plus-circle"></i> {{ $user->tasks_created_count }}
                                        </span>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded" title="Tareas asignadas">
                                            <i class="fas fa-check-circle"></i> {{ $user->tasks_assigned_count }}
                                        </span>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded" title="Comentarios">
                                            <i class="fas fa-comments"></i> {{ $user->comments_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- View Profile -->
                                        <button onclick="openProfileModal({{ $user->id }}, '{{ $user->name }}')"
                                            class="text-green-600 hover:text-green-800 transition p-2" title="Ver Perfil">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>

                                        <!-- Edit -->
                                        <button
                                            onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->employee_id ?? '' }}', '{{ $user->role }}', '{{ $user->department }}', '{{ $user->position }}', {{ $user->is_active ? 'true' : 'false' }})"
                                            class="text-blue-600 hover:text-blue-800 transition p-2" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Toggle Status -->
                                        @if(auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('admin.toggle-user-status', $user) }}"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('¿Cambiar estado del usuario?')"
                                                    class="text-{{ $user->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $user->is_active ? 'yellow' : 'green' }}-800 transition p-2"
                                                    title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }}"></i>
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('admin.delete-user', $user) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('¿Estás seguro de eliminar este usuario? Se desactivará permanentemente.')"
                                                    class="text-red-600 hover:text-red-800 transition p-2" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Tú mismo</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-edit text-purple-600"></i> Editar Usuario
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="editUserForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombre *</label>
                        <input type="text" name="name" id="editName" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email *</label>
                        <input type="email" name="email" id="editEmail" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Cédula de Identidad</label>
                        <input type="text" name="employee_id" id="editEmployeeId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Rol *</label>
                        <select name="role" id="editRole"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="usuario">Usuario</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Departamento</label>
                        <input type="text" name="department" id="editDepartment"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Posición</label>
                        <input type="text" name="position" id="editPosition"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Estado *</label>
                        <select name="is_active" id="editIsActive"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeEditModal()"
                        class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition font-semibold">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition font-semibold">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Profile Modal -->
    <div id="profileModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-circle text-green-600"></i>
                    Perfil de <span id="profileUserName"></span>
                </h3>
                <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600" id="profileTasksCreated">0</p>
                    <p class="text-sm text-gray-600">Tareas Creadas</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600" id="profileTasksAssigned">0</p>
                    <p class="text-sm text-gray-600">Tareas Asignadas</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600" id="profileTasksCompleted">0</p>
                    <p class="text-sm text-gray-600">Completadas</p>
                </div>
            </div>

            <!-- Activity Calendar -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">
                    <i class="fas fa-calendar-alt text-green-600"></i> Calendario de Actividad
                </h4>
                <p class="text-sm text-gray-500 mb-4">Días con tareas asignadas o completadas en los últimos 6 meses</p>

                <div class="overflow-x-auto">
                    <div id="profile-activity-calendar" class="flex flex-col gap-1">
                        <p class="text-gray-400 text-center py-8">Cargando...</p>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-end gap-2 mt-4 text-sm text-gray-500">
                    <span>Menos</span>
                    <div class="w-4 h-4 rounded-sm bg-gray-200 border border-gray-300"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-300"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-400"></div>
                    <div class="w-4 h-4 rounded-sm bg-green-600"></div>
                    <span>Más</span>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="closeProfileModal()"
                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition font-semibold">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Edit User Modal
        function openEditModal(id, name, email, employeeId, role, department, position, isActive) {
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editEmployeeId').value = employeeId || '';
            document.getElementById('editRole').value = role;
            document.getElementById('editDepartment').value = department || '';
            document.getElementById('editPosition').value = position || '';
            document.getElementById('editIsActive').value = isActive ? '1' : '0';
            document.getElementById('editUserForm').action = `/admin/users/${id}`;
            document.getElementById('editUserModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        // Close modal on background click
        document.getElementById('editUserModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Profile Modal
            function openProfileModal(userId, userName) {
                document.getElementById('profileUserName').textContent = userName;
                document.getElementById('profileModal').classList.remove('hidden');
                document.getElementById('profile-activity-calendar').innerHTML = '<p class="text-gray-400 text-center py-8">Cargando...</p>';

                // Fetch activity data
                fetch(`/admin/users/${userId}/activity-calendar`)
                    .then(res => res.json())
                    .then(data => {
                        // Update stats
                        document.getElementById('profileTasksCreated').textContent = data.stats.tasks_created;
                        document.getElementById('profileTasksAssigned').textContent = data.stats.tasks_assigned;
                        document.getElementById('profileTasksCompleted').textContent = data.stats.tasks_completed;

                        // Generate calendar
                        generateProfileCalendar(data.activityCalendar);
                    })
                    .catch(err => {
                        document.getElementById('profile-activity-calendar').innerHTML = '<p class="text-red-500 text-center py-8">Error al cargar datos</p>';
                    });
            }

            function closeProfileModal() {
                document.getElementById('profileModal').classList.add('hidden');
            }

            document.getElementById('profileModal')?.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeProfileModal();
                }
            });

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

            // Generate activity calendar for profile modal
            function generateProfileCalendar(activityData) {
                const container = document.getElementById('profile-activity-calendar');
                if (!container) return;

                const today = new Date();
                const sixMonthsAgo = new Date(today);
                sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);

                // Find max activity for color scaling
                const values = Object.values(activityData);
                const maxActivity = values.length > 0 ? Math.max(...values, 1) : 1;

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
                currentDate.setDate(currentDate.getDate() - currentDate.getDay());

                let weekHtml = '';
                let weekCount = 0;

                while (currentDate <= today) {
                    const dateStr = currentDate.toISOString().split('T')[0];
                    const dayOfWeek = currentDate.getDay();
                    const activity = activityData[dateStr] || 0;

                    // Color based on activity level
                    let colorClass = 'bg-gray-200 border border-gray-300';
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

                        if (currentDate.getMonth() !== currentMonth) {
                            const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            monthsHtml += `<span style="width: ${weekCount * 17}px">${monthNames[currentDate.getMonth()]}</span>`;
                            currentMonth = currentDate.getMonth();
                            weekCount = 0;
                        }
                    }

                    // Spanish tooltip
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

                // Day labels
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

            // Client-side filtering
            document.getElementById('searchUsers').addEventListener('input', filterUsers);
            document.getElementById('filterRole').addEventListener('change', filterUsers);
            document.getElementById('filterStatus').addEventListener('change', filterUsers);

            function filterUsers() {
                const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
                const roleFilter = document.getElementById('filterRole').value;
                const statusFilter = document.getElementById('filterStatus').value;

                const rows = document.querySelectorAll('.user-row');

                rows.forEach(row => {
                    const name = row.dataset.name;
                    const email = row.dataset.email;
                    const role = row.dataset.role;
                    const status = row.dataset.status;

                    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesRole = !roleFilter || role === roleFilter;
                    const matchesStatus = !statusFilter || status === statusFilter;

                    if (matchesSearch && matchesRole && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>
@endsection