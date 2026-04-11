@extends('layouts.app')

@section('title', 'Tareas')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Mis Tareas</h1>
                <p class="text-gray-600">Gestiona y organiza tu trabajo de manera eficiente</p>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Vista -->
                    <div>
                        <label for="view" class="block text-sm font-semibold text-gray-700 mb-2">Vista</label>
                        <select id="view"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                            <option value="my_tasks" {{ request('view', 'all') === 'my_tasks' ? 'selected' : '' }}>Mis Tareas
                            </option>
                            <option value="my_tracking" {{ request('view', 'all') === 'my_tracking' ? 'selected' : '' }}>Mis
                                Seguimientos</option>
                            <option value="all" {{ request('view', 'all') === 'all' ? 'selected' : '' }}>Todas</option>
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                        <select id="status"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                            <option value="">Todos</option>
                            <option value="Pendiente" {{ request('status') === 'Pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="En progreso" {{ request('status') === 'En progreso' ? 'selected' : '' }}>En
                                Progreso</option>
                            <option value="Pendiente de Aprobación" {{ request('status') === 'Pendiente de Aprobación' ? 'selected' : '' }}>Pendiente de Aprobación</option>
                            <option value="Completada" {{ request('status') === 'Completada' ? 'selected' : '' }}>Completada
                            </option>
                        </select>
                    </div>

                    <!-- Prioridad -->
                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">Prioridad</label>
                        <select id="priority"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                            <option value="">Todas</option>
                            <option value="Baja" {{ request('priority') === 'Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ request('priority') === 'Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ request('priority') === 'Alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <!-- Búsqueda -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Búsqueda</label>
                        <div class="relative">
                            <input type="text" id="search" value="{{ request('search') }}" placeholder="Buscar tareas..."
                                class="w-full px-4 py-2.5 pl-11 bg-white border border-gray-300 rounded-lg text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Actions Row -->
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('tasks.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-plus"></i>
                        Nueva Tarea
                    </a>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($tasks as $task)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow relative overflow-hidden">
                        <!-- Expired Label -->
                        @if($task->isOverdue())
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    Vencida
                                </span>
                            </div>
                        @endif

                        <!-- Priority Indicator -->
                        <div
                            class="h-1.5 rounded-t-lg
                                                                                                                                                @if($task->priority === 'Alta') bg-red-500
                                                                                                                                                @elseif($task->priority === 'Media') bg-amber-500
                                                                                                                                                @else bg-green-500
                                                                                                                                                @endif">
                        </div>

                        <!-- Card Content -->
                        <div class="p-5">
                            <!-- Title -->
                            <a href="{{ route('tasks.show', $task) }}"
                                class="block text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors mb-3 line-clamp-2">
                                {{ $task->title }}
                            </a>

                            <!-- Description -->
                            @if($task->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $task->description }}</p>
                            @endif

                            <!-- Ownership Badges -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                @if($task->creator_id !== $task->assignee_id && $task->assignee_id === auth()->id())
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fas fa-user-tag mr-1"></i> Asignada
                                    </span>
                                @elseif($task->creator_id === auth()->id() && $task->assignee_id === auth()->id())
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                        <i class="fas fa-user mr-1"></i> Propia
                                    </span>
                                @endif
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <!-- Priority Badge -->
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold
                                                                                                                                                        @if($task->priority === 'Alta') bg-red-50 text-red-700 border border-red-200
                                                                                                                                                        @elseif($task->priority === 'Media') bg-amber-50 text-amber-700 border border-amber-200
                                                                                                                                                        @else bg-green-50 text-green-700 border border-green-200
                                                                                                                                                        @endif">
                                    {{ $task->priority }}
                                </span>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold
                                            @if($task->status === 'Completada') bg-green-50 text-green-700 border border-green-200
                                            @elseif($task->status === 'En progreso') bg-blue-50 text-blue-700 border border-blue-200
                                            @elseif($task->status === 'Pendiente de Aprobación') bg-amber-50 text-amber-700 border border-amber-200
                                            @else bg-gray-50 text-gray-700 border border-gray-200
                                            @endif">
                                    @if($task->status === 'Pendiente de Aprobación')
                                        ⏰
                                    @endif
                                    {{ $task->status }}
                                </span>
                            </div>

                            <!-- User Info -->
                            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                                <div
                                    class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-semibold text-sm">
                                    {{ substr($task->assignee->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $task->assignee->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $task->assignee->department }}</p>
                                    @if($task->creator_id !== $task->assignee_id)
                                        <p class="text-xs text-blue-600 truncate">
                                            <i class="fas fa-user-plus"></i> Asignada por {{ $task->creator->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Footer with Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="flex-1 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-center text-sm transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>
                                <button onclick="openChatModal({{ $task->id }})"
                                    class="flex-1 px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 font-semibold rounded-lg text-sm transition-colors">
                                    <i class="fas fa-comments mr-1"></i> Chat ({{ $task->comments_count }})
                                </button>
                            </div>

                            <!-- Time Info (small, above buttons) -->
                            <div class="flex justify-between items-center text-xs mb-2">
                                @if($task->isOverdue())
                                    {{-- Ya se muestra etiqueta arriba, solo mostrar tiempo restante --}}
                                    <span class="text-gray-400 countdown-timer" data-due="{{ $task->due_date->timestamp }}"></span>
                                @elseif($task->isDueSoon())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 font-medium rounded-md text-xs border border-amber-200 countdown-timer" data-due="{{ $task->due_date->timestamp }}">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        {{ $task->getTimeRemainingAttribute() }}
                                    </span>
                                @else
                                    <span class="text-gray-500 countdown-timer" data-due="{{ $task->due_date->timestamp }}">
                                        {{ $task->getTimeRemainingAttribute() }}
                                    </span>
                                @endif
                                <span class="text-gray-600 font-medium">{{ $task->due_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <div class="inline-block p-4 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-inbox text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay tareas</h3>
                            <p class="text-gray-600 mb-6">Comienza creando tu primera tarea</p>
                            <a href="{{ route('tasks.create') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-plus"></i>
                                Nueva Tarea
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
                <div class="mt-8">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Chat Modal -->
    <div id="chat-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-comments text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-task-title">Chat</h3>
                </div>
                <button onclick="closeChatModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Messages (fixed height with scroll) -->
            <div id="modal-messages" class="overflow-y-auto p-4 space-y-4 bg-gray-50"
                style="height: 500px; max-height: 500px;">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Input -->
            <div class="p-4 border-t border-gray-200">
                <form id="modal-chat-form" enctype="multipart/form-data">
                    @csrf

                    <!-- @ Mention Dropdown (hidden by default) -->
                    <div id="mention-dropdown"
                        class="hidden mb-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <div class="p-2 text-xs text-gray-500 border-b bg-gray-50">
                            <i class="fas fa-at text-blue-500"></i> Escribe para filtrar
                        </div>
                        <div id="mention-list" class="divide-y divide-gray-100">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <!-- File Preview -->
                    <div id="modal-file-preview" class="hidden mb-2 p-2 bg-gray-100 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-paperclip text-gray-500"></i>
                                <span id="modal-file-name" class="text-sm text-gray-600"></span>
                            </div>
                            <button type="button" onclick="clearModalFile()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <label
                            class="w-10 h-10 shrink-0 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors cursor-pointer">
                            <i class="fas fa-paperclip text-xl"></i>
                            <input type="file" name="attachments[]" id="modal-file-input" multiple
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                                onchange="handleModalFileSelect(this)">
                        </label>

                        <input type="text" name="content" id="modal-chat-input"
                            class="flex-1 min-w-0 px-4 py-2.5 bg-gray-100 border-0 rounded-full focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all text-sm"
                            placeholder="Escribe @ para mencionar..." autocomplete="off">

                        <button type="submit"
                            class="w-10 h-10 shrink-0 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-all hover:scale-105 shadow-md">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- User data for mentions (hidden) -->
            <script id="mention-users-data" type="application/json">
                                {!! json_encode($activeUsers->map(function ($u) {
        return ['email' => $u->email, 'name' => $u->name, 'dept' => $u->department, 'pos' => $u->position]; })->values()) !!}
                            </script>
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const viewSelect = document.getElementById('view');
            const statusSelect = document.getElementById('status');
            const prioritySelect = document.getElementById('priority');
            const searchInput = document.getElementById('search');

            function applyFilters() {
                const view = viewSelect.value;
                const status = statusSelect.value;
                const priority = prioritySelect.value;
                const search = searchInput.value;

                let url = '{{ route('tasks.index') }}?';
                const params = new URLSearchParams();

                if (view) params.append('view', view);
                if (status) params.append('status', status);
                if (priority) params.append('priority', priority);
                if (search) params.append('search', search);

                window.location.href = url + params.toString();
            }

            viewSelect.addEventListener('change', applyFilters);
            statusSelect.addEventListener('change', applyFilters);
            prioritySelect.addEventListener('change', applyFilters);

            // Debounce search
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });

            // Real-time countdown timer (updates every second)
            function updateCountdowns() {
                const timers = document.querySelectorAll('.countdown-timer');
                const now = Math.floor(Date.now() / 1000);

                timers.forEach(timer => {
                    const dueTimestamp = parseInt(timer.dataset.due);
                    const diff = dueTimestamp - now;

                    if (diff <= 0) {
                        // No mostrar texto, ya hay etiqueta arriba
                        timer.textContent = '';
                    } else {
                        const days = Math.floor(diff / 86400);
                        const hours = Math.floor((diff % 86400) / 3600);
                        const minutes = Math.floor((diff % 3600) / 60);
                        const seconds = diff % 60;

                        if (days > 0) {
                            timer.textContent = `${days}d ${hours}h ${minutes}m`;
                        } else if (hours > 0) {
                            timer.textContent = `${hours}h ${minutes}m ${seconds}s`;
                        } else if (minutes > 0) {
                            timer.textContent = `${minutes}m ${seconds}s`;
                        } else {
                            timer.textContent = `${seconds}s`;
                        }
                    }
                });
            }

            // Update every second for real-time feeling
            updateCountdowns();
            setInterval(updateCountdowns, 1000);
        });

        // Chat modal functionality
        function openChatModal(taskId) {
            fetch(`/tasks/${taskId}/comments`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modal-task-title').textContent = data.task.title;
                    const messagesContainer = document.getElementById('modal-messages');
                    messagesContainer.innerHTML = '';

                    if (data.comments.length === 0) {
                        messagesContainer.innerHTML = `
                                                                            <div class="text-center py-8 text-gray-500">
                                                                                <i class="fas fa-comments text-4xl mb-2"></i>
                                                                                <p>No hay mensajes aún</p>
                                                                            </div>
                                                                        `;
                    } else {
                        data.comments.forEach(comment => {
                            const isOwn = comment.user_id === {{ auth()->id() }};
                            const messageDiv = document.createElement('div');
                            messageDiv.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;

                            // Build attachments HTML
                            let attachmentsHtml = '';
                            if (comment.attachments && comment.attachments.length > 0) {
                                attachmentsHtml = '<div class="mt-2 space-y-1">';
                                comment.attachments.forEach(att => {
                                    if (att.mime_type && att.mime_type.startsWith('image/')) {
                                        attachmentsHtml += `<img src="/storage/${att.file_path}" alt="${att.file_name}" class="rounded max-w-full" style="max-height:150px">`;
                                    } else {
                                        attachmentsHtml += `<a href="/attachments/${att.id}/download" class="flex items-center gap-2 p-2 rounded ${isOwn ? 'bg-blue-500/50' : 'bg-gray-100'} text-xs"><i class="fas fa-file"></i>${att.file_name}</a>`;
                                    }
                                });
                                attachmentsHtml += '</div>';
                            }
                            // Determine check icon based on read status
                            let checkIcon = '';
                            if (isOwn) {
                                // Check if assignee or creator has read it
                                const readBy = comment.read_by || [];
                                const hasBeenRead = readBy.length > 0;
                                checkIcon = hasBeenRead
                                    ? '<span class="inline-flex items-center text-blue-200 ml-2" title="Visto"><i class="fas fa-check text-xs"></i><i class="fas fa-check text-xs -ml-1.5"></i></span>'
                                    : '<i class="fas fa-check text-xs text-blue-200/60 ml-2" title="Enviado"></i>';
                            }

                            messageDiv.innerHTML = `
                                                        <div class="max-w-[70%]">
                                                            <div class="p-3 rounded-lg ${isOwn ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-900'}">
                                                                ${!isOwn ? `<p class="text-xs font-semibold text-blue-600 mb-1">${comment.user.name}</p>` : ''}
                                                                ${comment.content ? `<p class="text-sm">${comment.content}</p>` : ''}
                                                                ${attachmentsHtml}
                                                                <div class="flex items-center justify-end gap-1 mt-1">
                                                                    <span class="text-[10px] ${isOwn ? 'text-blue-200' : 'text-gray-400'}">${new Date(comment.created_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</span>
                                                                    ${checkIcon}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;
                            messagesContainer.appendChild(messageDiv);
                        });

                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }

                    document.getElementById('chat-modal').dataset.taskId = taskId;
                    document.getElementById('chat-modal').classList.remove('hidden');

                    // Mark comments as read
                    fetch(`/tasks/${taskId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).catch(err => console.log('Mark read error:', err));
                });
        }

        function closeChatModal() {
            document.getElementById('chat-modal').classList.add('hidden');
        }

        // WhatsApp-style @ Mention System
        let mentionUsers = [];
        let mentionStartPos = -1;
        let selectedMentionIndex = 0;

        // Load user data
        document.addEventListener('DOMContentLoaded', function () {
            const dataScript = document.getElementById('mention-users-data');
            if (dataScript) {
                try {
                    mentionUsers = JSON.parse(dataScript.textContent);
                } catch (e) {
                    console.error('Error loading mention users:', e);
                }
            }

            const input = document.getElementById('modal-chat-input');
            if (input) {
                input.addEventListener('input', handleMentionInput);
                input.addEventListener('keydown', handleMentionKeydown);
                input.addEventListener('blur', () => setTimeout(hideMentionDropdown, 150));
            }
        });

        function handleMentionInput(e) {
            const input = e.target;
            const value = input.value;
            const cursorPos = input.selectionStart;

            // Find the last @ before cursor
            const textBeforeCursor = value.substring(0, cursorPos);
            const lastAtIndex = textBeforeCursor.lastIndexOf('@');

            if (lastAtIndex !== -1) {
                // Check if @ is at start or preceded by space
                if (lastAtIndex === 0 || textBeforeCursor[lastAtIndex - 1] === ' ') {
                    const searchTerm = textBeforeCursor.substring(lastAtIndex + 1).toLowerCase();

                    // Only show if no space after @
                    if (!searchTerm.includes(' ')) {
                        mentionStartPos = lastAtIndex;
                        showMentionDropdown(searchTerm);
                        return;
                    }
                }
            }

            hideMentionDropdown();
        }

        function handleMentionKeydown(e) {
            const dropdown = document.getElementById('mention-dropdown');
            if (dropdown.classList.contains('hidden')) return;

            const items = dropdown.querySelectorAll('.mention-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedMentionIndex = Math.min(selectedMentionIndex + 1, items.length - 1);
                updateMentionSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedMentionIndex = Math.max(selectedMentionIndex - 1, 0);
                updateMentionSelection(items);
            } else if (e.key === 'Enter' && items.length > 0) {
                e.preventDefault();
                const selectedItem = items[selectedMentionIndex];
                if (selectedItem) {
                    selectMention(selectedItem.dataset.email);
                }
            } else if (e.key === 'Escape') {
                hideMentionDropdown();
            }
        }

        function showMentionDropdown(searchTerm) {
            const dropdown = document.getElementById('mention-dropdown');
            const list = document.getElementById('mention-list');

            // Filter users
            const filtered = mentionUsers.filter(u =>
                u.name.toLowerCase().includes(searchTerm) ||
                u.email.toLowerCase().includes(searchTerm) ||
                (u.dept && u.dept.toLowerCase().includes(searchTerm))
            ).slice(0, 8); // Limit to 8 results

            if (filtered.length === 0) {
                list.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No se encontraron usuarios</div>';
            } else {
                list.innerHTML = filtered.map((u, i) => `
                                    <div class="mention-item p-2 hover:bg-blue-50 cursor-pointer flex items-center gap-2 ${i === 0 ? 'bg-blue-50' : ''}"
                                         data-email="${u.email}"
                                         onclick="selectMention('${u.email}')">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                            ${u.name.charAt(0).toUpperCase()}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-sm text-gray-800 truncate">${u.name}</div>
                                            <div class="text-xs text-gray-500 truncate">${u.dept || u.pos || u.email}</div>
                                        </div>
                                    </div>
                                `).join('');
            }

            selectedMentionIndex = 0;
            dropdown.classList.remove('hidden');
        }

        function hideMentionDropdown() {
            document.getElementById('mention-dropdown')?.classList.add('hidden');
            mentionStartPos = -1;
        }

        function updateMentionSelection(items) {
            items.forEach((item, i) => {
                item.classList.toggle('bg-blue-50', i === selectedMentionIndex);
            });
        }

        function selectMention(email) {
            const input = document.getElementById('modal-chat-input');
            if (!input || mentionStartPos === -1) return;

            const value = input.value;
            const before = value.substring(0, mentionStartPos);
            const after = value.substring(input.selectionStart);

            input.value = before + '@' + email + ' ' + after;
            input.focus();

            // Set cursor after mention
            const newPos = mentionStartPos + email.length + 2;
            input.setSelectionRange(newPos, newPos);

            hideMentionDropdown();
        }

        // Legacy function for compatibility
        function addMention(email) {
            const input = document.getElementById('modal-chat-input');
            if (input) {
                input.value = '@' + email + ' ' + input.value;
                input.focus();
            }
        }

        // File preview handlers
        function handleModalFileSelect(fileInput) {
            const preview = document.getElementById('modal-file-preview');
            const fileName = document.getElementById('modal-file-name');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                fileName.textContent = `${file.name} (${sizeMB} MB)`;
                preview.classList.remove('hidden');
            }
        }

        function clearModalFile() {
            const fileInput = document.getElementById('modal-file-input');
            const preview = document.getElementById('modal-file-preview');
            if (fileInput) fileInput.value = '';
            if (preview) preview.classList.add('hidden');
        }

        // Handle form submit
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('modal-chat-form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const taskId = document.getElementById('chat-modal').dataset.taskId;
                    const contentInput = document.getElementById('modal-chat-input');
                    const fileInput = document.getElementById('modal-file-input');

                    const hasContent = contentInput && contentInput.value.trim();
                    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

                    // Validate: need content OR file
                    if (!hasContent && !hasFile) {
                        alert('Por favor escribe un mensaje o adjunta un archivo');
                        return;
                    }

                    const formData = new FormData(form);

                    fetch(`/tasks/${taskId}/comments`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                form.reset();
                                clearModalFile();
                                openChatModal(taskId);
                            } else {
                                let errorMsg = data.message || 'Error al enviar mensaje';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).flat().join('\n');
                                }
                                alert(errorMsg);
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Error al enviar mensaje');
                        });
                });
            }
        });
    </script>
@endsection