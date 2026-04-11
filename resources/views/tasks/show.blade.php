@extends('layouts.app')

@section('title', $task->title)

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header with Back Button -->
            <div class="mb-6">
                <a href="{{ route('tasks.index') }}"
                    class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold">
                    <i class="fas fa-arrow-left"></i> Volver a tareas
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content: Task Details -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Task Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <!-- Priority Band -->
                        <div class="h-1.5 rounded-t-lg
                                                                                                        @if($task->priority === 'Alta') bg-red-500
                                                                                                        @elseif($task->priority === 'Media') bg-amber-500
                                                                                                        @else bg-green-500
                                                                                                        @endif">
                        </div>

                        <div class="p-6">
                            <!-- Title -->
                            <h1 class="text-3xl font-bold text-gray-900 mb-4 break-all"
                                style="word-wrap: break-word; overflow-wrap: break-word;">{{ $task->title }}</h1>

                            <!-- Creator Info -->
                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                                <i class="fas fa-user"></i>
                                <span>Creado por <strong>{{ $task->creator->name }}</strong></span>
                                <span class="text-gray-400">•</span>
                                <span>{{ $task->created_at->diffForHumans() }}</span>
                            </div>

                            <!-- Description -->
                            @if($task->description)
                                <div class="mb-6">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Descripción</h3>
                                    <p class="text-gray-600 break-all overflow-hidden"
                                        style="word-wrap: break-word; overflow-wrap: break-word;">{{ $task->description }}</p>
                                </div>
                            @endif

                            <!-- Quick Status Buttons (Only for assigned user when NOT pending approval) -->
                            @if(auth()->id() === $task->assignee_id && $task->status !== 'Pendiente de Aprobación')
                                <div class="mb-6">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Cambiar Estado</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Pendiente">
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors
                                                                                                                                                                                                                {{ $task->status === 'Pendiente' ? 'bg-gray-200 text-gray-800' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                                ⏳ Pendiente
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="En progreso">
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors
                                                                                                                                                                                                                {{ $task->status === 'En progreso' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                                                🚀 En Progreso
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Completada">
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors
                                                                                                                                                                                                                {{ $task->status === 'Completada' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                                ✅ Completada
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <!-- Pending Approval Banner (for assignee) -->
                            @if($task->status === 'Pendiente de Aprobación' && auth()->id() === $task->assignee_id)
                                <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="text-3xl">⏳</div>
                                        <div>
                                            <h4 class="font-semibold text-amber-800">Esperando Aprobación</h4>
                                            <p class="text-sm text-amber-600">Tu trabajo está pendiente de revisión por
                                                <strong>{{ $task->creator->name }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Approval Action Banner (for creator/admin) -->
                            @if($task->status === 'Pendiente de Aprobación' && (auth()->id() === $task->creator_id || auth()->user()->isAdmin()))
                                <div class="mb-6 p-5 bg-white border-2 border-amber-300 rounded-xl shadow-lg">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center text-3xl">
                                            📋
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-xl text-gray-800 mb-1">⚡ Aprobación Requerida</h4>
                                            <p class="text-gray-600 mb-4">
                                                <strong class="text-amber-700">{{ $task->assignee->name }}</strong> marcó esta
                                                tarea como completada y requiere tu aprobación.
                                            </p>
                                            <div class="flex flex-wrap gap-3">
                                                <form method="POST" action="{{ route('tasks.approve', $task) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                                                        ✅ Aprobar Tarea
                                                    </button>
                                                </form>
                                                <button type="button" onclick="openRejectModal()"
                                                    class="px-6 py-3 bg-white border-2 border-red-400 text-red-600 font-bold rounded-lg hover:bg-red-50 hover:border-red-500 transition-all flex items-center gap-2">
                                                    ❌ Rechazar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Metadata Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Status -->
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">Estado</p>
                                    <span class="inline-flex items-center gap-1 font-semibold text-sm
                                                @if($task->status === 'Completada') text-green-700
                                                @elseif($task->status === 'En progreso') text-blue-700
                                                @elseif($task->status === 'Pendiente de Aprobación') text-amber-700
                                                @else text-gray-700
                                                @endif">
                                        @if($task->status === 'Completada') ✅
                                        @elseif($task->status === 'En progreso') 🚀
                                        @elseif($task->status === 'Pendiente de Aprobación') ⏰
                                        @else ⏳
                                        @endif
                                        {{ $task->status }}
                                    </span>
                                    @if($task->status === 'Completada' && $task->approved_at)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Aprobada por {{ $task->approver?->name ?? 'Sistema' }} el
                                            {{ $task->approved_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Priority -->
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">Prioridad</p>
                                    <span
                                        class="inline-flex items-center gap-1 font-semibold text-sm
                                                                                                                    @if($task->priority === 'Alta') text-red-700
                                                                                                                    @elseif($task->priority === 'Media') text-amber-700
                                                                                                                    @else text-green-700
                                                                                                                    @endif">
                                        @if($task->priority === 'Alta') 🔴
                                        @elseif($task->priority === 'Media') 🟡
                                        @else 🟢
                                        @endif
                                        {{ $task->priority }}
                                    </span>
                                </div>

                                <!-- Assigned To -->
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">Asignado a</p>
                                    <p class="font-semibold text-sm text-gray-900">{{ $task->assignee->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $task->assignee->department }}</p>
                                </div>

                                <!-- Due Date -->
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs font-semibold text-gray-600 mb-1">Fecha Límite</p>
                                    <p
                                        class="font-semibold text-sm
                                                                                                                    @if($task->isOverdue()) text-red-700
                                                                                                                    @elseif($task->isDueSoon()) text-amber-700
                                                                                                                    @else text-gray-900
                                                                                                                    @endif">
                                        {{ $task->due_date->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        @if($task->isOverdue())
                                            Vencida
                                        @else
                                            {{ $task->getTimeRemainingAttribute() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>

                        <!-- Chat Button - Prominent -->
                        <button onclick="openTaskChat()"
                            class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-center transition-colors mb-3">
                            <i class="fas fa-comments mr-2"></i>Abrir Chat
                            @if($task->comments->count() > 0)
                                <span
                                    class="ml-2 px-2 py-0.5 bg-blue-500 rounded-full text-xs">{{ $task->comments->count() }}</span>
                            @endif
                        </button>

                        @if(auth()->user()->id === $task->creator_id || auth()->user()->isAdmin())
                            @if(!$task->archived_at)
                                <form method="POST" action="{{ route('tasks.archive', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('¿Archivar esta tarea?')"
                                        class="block w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-lg text-center transition-colors">
                                        <i class="fas fa-archive mr-2"></i>Archivar
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Enhanced Chat Modal -->
    <div id="task-chat-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden">

            <!-- Header - WhatsApp style -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-comments text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white">{{ Str::limit($task->title, 30) }}</h3>
                        <p class="text-blue-100 text-xs">
                            <i class="fas fa-users mr-1"></i>
                            {{ $task->comments->pluck('user.name')->unique()->count() }} participantes
                        </p>
                    </div>
                </div>
                <button onclick="closeTaskChat()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Messages Container -->
            <div id="task-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-100"
                style="min-height: 350px; max-height: 450px;">

                @forelse($task->comments as $comment)
                    @php $isOwn = $comment->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} animate-fadeIn">
                        <!-- Avatar for others -->
                        @if(!$isOwn)
                            <div
                                class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-sm mr-2 flex-shrink-0">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                        @endif

                        <div
                            class="max-w-[75%] {{ $isOwn ? 'bg-blue-500 text-white rounded-l-xl rounded-tr-xl' : 'bg-white text-gray-800 rounded-r-xl rounded-tl-xl shadow-sm' }} p-3 relative">
                            <!-- Tail -->
                            <div
                                class="absolute top-0 {{ $isOwn ? '-right-2 border-l-8 border-l-blue-500' : '-left-2 border-r-8 border-r-white' }} border-t-8 border-t-transparent border-b-0 w-0 h-0">
                            </div>

                            @if(!$isOwn)
                                <p class="font-semibold text-sm text-blue-600 mb-1">{{ $comment->user->name }}</p>
                            @endif

                            <p class="text-sm leading-relaxed">{{ $comment->content }}</p>

                            <!-- Attachments -->
                            @if($comment->attachments && $comment->attachments->count() > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($comment->attachments as $attachment)
                                        @if(Str::startsWith($attachment->mime_type, 'image/'))
                                            <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}"
                                                class="rounded-lg max-w-full cursor-pointer hover:opacity-90 transition-opacity"
                                                onclick="showImageModal('{{ asset('storage/' . $attachment->file_path) }}')"
                                                style="max-height: 200px;">
                                        @else
                                            <a href="{{ route('attachments.download', $attachment) }}"
                                                class="flex items-center gap-2 p-2 rounded-lg {{ $isOwn ? 'bg-blue-400/50 hover:bg-blue-400/70' : 'bg-gray-100 hover:bg-gray-200' }} transition-colors">
                                                <i class="fas fa-file-alt"></i>
                                                <span class="text-xs truncate">{{ $attachment->file_name }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <!-- Time & Status -->
                            <div class="flex items-center justify-end gap-2 mt-1">
                                <span class="text-[10px] {{ $isOwn ? 'text-blue-100' : 'text-gray-400' }}">
                                    {{ $comment->created_at->format('H:i') }}
                                </span>
                                @if($isOwn)
                                    @php
                                        // Check if the task assignee (recipient) has read the message
                                        $recipientId = $task->assignee_id !== auth()->id() ? $task->assignee_id : $task->creator_id;
                                        $isRead = in_array($recipientId, $comment->read_by ?? []);
                                    @endphp
                                    @if($isRead)
                                        <span class="flex items-center gap-0.5 text-blue-200" title="Visto">
                                            <i class="fas fa-check text-xs"></i><i class="fas fa-check text-xs -ml-1.5"></i>
                                        </span>
                                    @else
                                        <i class="fas fa-check text-xs text-blue-200/60" title="Enviado"></i>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-400 py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-comments text-3xl text-gray-400"></i>
                            </div>
                            <p class="font-medium">No hay mensajes aún</p>
                            <p class="text-sm">¡Sé el primero en escribir!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- @ Mention Dropdown (hidden by default) -->
            <div id="show-mention-dropdown"
                class="hidden mx-4 mb-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                <div class="p-2 text-xs text-gray-500 border-b bg-gray-50">
                    <i class="fas fa-at text-blue-500"></i> Escribe para filtrar
                </div>
                <div id="show-mention-list" class="divide-y divide-gray-100">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <!-- User data for mentions (hidden) -->
            <script id="show-mention-users-data" type="application/json">
                                                {!! json_encode($activeUsers->map(function ($u) {
        return ['email' => $u->email, 'name' => $u->name, 'dept' => $u->department, 'pos' => $u->position]; })->values()) !!}
                                            </script>

            <!-- Input Area - Modern Design -->
            <div class="p-3 border-t border-gray-200 bg-white">
                <form id="show-chat-form" method="POST" action="/tasks/{{ $task->id }}/comments"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- File Preview Area -->
                    <div id="show-file-preview" class="hidden mb-2 p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-paperclip text-gray-500"></i>
                                <span id="show-file-name" class="text-sm text-gray-600"></span>
                            </div>
                            <button type="button" onclick="clearShowFile()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Attachment Button -->
                        <label
                            class="w-10 h-10 flex-shrink-0 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors cursor-pointer">
                            <i class="fas fa-paperclip text-xl"></i>
                            <input type="file" name="attachments[]" id="show-file-input" class="hidden"
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" onchange="handleShowFileSelect(this)">
                        </label>

                        <!-- Input -->
                        <input type="text" name="content" id="show-chat-input"
                            class="flex-1 min-w-0 px-4 py-2.5 bg-gray-100 border-0 rounded-full focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Escribe @ para mencionar..." autocomplete="off">

                        <!-- Send Button -->
                        <button type="submit"
                            class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-all hover:scale-105 shadow-md">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="image-modal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
        <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain">
    </div>

    <!-- Reject Task Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-4">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-times-circle"></i> Rechazar Tarea
                </h3>
            </div>
            <form method="POST" action="{{ route('tasks.reject', $task) }}" class="p-6">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo del Rechazo</label>
                    <textarea name="reason" rows="3" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Explica brevemente por qué rechazas esta tarea..."></textarea>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    La tarea volverá al estado "En progreso" y el asignado será notificado.
                </p>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function showImageModal(src) {
            document.getElementById('modal-image').src = src;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }

        function openRejectModal() {
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        function openTaskChat() {
            document.getElementById('task-chat-modal').classList.remove('hidden');
            const chatMessages = document.getElementById('task-chat-messages');
            if (chatMessages) {
                // Scroll to bottom immediately and after a delay
                chatMessages.scrollTop = chatMessages.scrollHeight;
                setTimeout(() => chatMessages.scrollTop = chatMessages.scrollHeight, 200);
            }
            document.getElementById('show-chat-input')?.focus();

            // Mark comments as read
            fetch('/tasks/{{ $task->id }}/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).catch(err => console.log('Mark read error:', err));
        }

        function closeTaskChat() {
            document.getElementById('task-chat-modal').classList.add('hidden');
        }

        // WhatsApp-style @ Mention System
        let showMentionUsers = [];
        let showMentionStartPos = -1;
        let showSelectedMentionIndex = 0;

        function initShowMentions() {
            const dataScript = document.getElementById('show-mention-users-data');
            if (dataScript) {
                try {
                    showMentionUsers = JSON.parse(dataScript.textContent);
                } catch (e) {
                    console.error('Error loading mention users:', e);
                }
            }

            const input = document.getElementById('show-chat-input');
            if (input) {
                input.addEventListener('input', handleShowMentionInput);
                input.addEventListener('keydown', handleShowMentionKeydown);
                input.addEventListener('blur', () => setTimeout(hideShowMentionDropdown, 150));
            }
        }

        function handleShowMentionInput(e) {
            const input = e.target;
            const value = input.value;
            const cursorPos = input.selectionStart;

            const textBeforeCursor = value.substring(0, cursorPos);
            const lastAtIndex = textBeforeCursor.lastIndexOf('@');

            if (lastAtIndex !== -1) {
                if (lastAtIndex === 0 || textBeforeCursor[lastAtIndex - 1] === ' ') {
                    const searchTerm = textBeforeCursor.substring(lastAtIndex + 1).toLowerCase();
                    if (!searchTerm.includes(' ')) {
                        showMentionStartPos = lastAtIndex;
                        showShowMentionDropdown(searchTerm);
                        return;
                    }
                }
            }
            hideShowMentionDropdown();
        }

        function handleShowMentionKeydown(e) {
            const dropdown = document.getElementById('show-mention-dropdown');
            if (dropdown.classList.contains('hidden')) return;

            const items = dropdown.querySelectorAll('.mention-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                showSelectedMentionIndex = Math.min(showSelectedMentionIndex + 1, items.length - 1);
                updateShowMentionSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                showSelectedMentionIndex = Math.max(showSelectedMentionIndex - 1, 0);
                updateShowMentionSelection(items);
            } else if (e.key === 'Enter' && items.length > 0) {
                e.preventDefault();
                const selectedItem = items[showSelectedMentionIndex];
                if (selectedItem) selectShowMention(selectedItem.dataset.email);
            } else if (e.key === 'Escape') {
                hideShowMentionDropdown();
            }
        }

        function showShowMentionDropdown(searchTerm) {
            const dropdown = document.getElementById('show-mention-dropdown');
            const list = document.getElementById('show-mention-list');

            const filtered = showMentionUsers.filter(u =>
                u.name.toLowerCase().includes(searchTerm) ||
                u.email.toLowerCase().includes(searchTerm) ||
                (u.dept && u.dept.toLowerCase().includes(searchTerm))
            ).slice(0, 8);

            if (filtered.length === 0) {
                list.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">No se encontraron usuarios</div>';
            } else {
                list.innerHTML = filtered.map((u, i) => `
                                                <div class="mention-item p-2 hover:bg-blue-50 cursor-pointer flex items-center gap-2 ${i === 0 ? 'bg-blue-50' : ''}"
                                                     data-email="${u.email}" onclick="selectShowMention('${u.email}')">
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

            showSelectedMentionIndex = 0;
            dropdown.classList.remove('hidden');
        }

        function hideShowMentionDropdown() {
            document.getElementById('show-mention-dropdown')?.classList.add('hidden');
            showMentionStartPos = -1;
        }

        function updateShowMentionSelection(items) {
            items.forEach((item, i) => item.classList.toggle('bg-blue-50', i === showSelectedMentionIndex));
        }

        function selectShowMention(email) {
            const input = document.getElementById('show-chat-input');
            if (!input || showMentionStartPos === -1) return;

            const value = input.value;
            const before = value.substring(0, showMentionStartPos);
            const after = value.substring(input.selectionStart);

            input.value = before + '@' + email + ' ' + after;
            input.focus();

            const newPos = showMentionStartPos + email.length + 2;
            input.setSelectionRange(newPos, newPos);
            hideShowMentionDropdown();
        }

        function addShowMention(email) {
            const input = document.getElementById('show-chat-input');
            if (input) {
                input.value = '@' + email + ' ' + input.value;
                input.focus();
            }
        }

        // Handle file selection
        function handleShowFileSelect(fileInput) {
            const preview = document.getElementById('show-file-preview');
            const fileName = document.getElementById('show-file-name');

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                fileName.textContent = `${file.name} (${sizeMB} MB)`;
                preview.classList.remove('hidden');
            }
        }

        // Clear file selection
        function clearShowFile() {
            const fileInput = document.getElementById('show-file-input');
            const preview = document.getElementById('show-file-preview');

            if (fileInput) fileInput.value = '';
            if (preview) preview.classList.add('hidden');
        }

        // Scroll to bottom on load and close modal on Escape
        document.addEventListener('DOMContentLoaded', function () {
            const chatMessages = document.getElementById('task-chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Initialize mention system
            initShowMentions();

            // Close modal on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeTaskChat();
                    closeImageModal();
                }
            });

            // Handle Chat Form Submit via AJAX
            const chatForm = document.getElementById('show-chat-form');
            if (chatForm) {
                chatForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Get references to inputs
                    const contentInput = document.getElementById('show-chat-input');
                    const fileInput = document.getElementById('show-file-input');

                    const hasContent = contentInput && contentInput.value.trim();
                    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

                    // Validate: need at least content OR file
                    if (!hasContent && !hasFile) {
                        alert('Por favor escribe un mensaje o adjunta un archivo');
                        return;
                    }

                    // Use FormData from the form element - this properly captures the file input
                    const formData = new FormData(chatForm);

                    // Debug: Log FormData contents
                    console.log('=== Sending FormData ===');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ':', pair[1]);
                    }

                    // Send with fetch - use form.action to respect current domain
                    fetch(chatForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json().then(data => ({
                                status: response.status,
                                ok: response.ok,
                                data: data
                            }));
                        })
                        .then(result => {
                            console.log('Response data:', result);

                            if (result.ok && result.data.success) {
                                // Clear inputs
                                contentInput.value = '';
                                clearShowFile();

                                // Reload page with chat open
                                window.location.href = window.location.pathname + "?open_chat=1";
                            } else {
                                // Handle errors from Laravel validation or logic
                                let errorMsg = 'Error al enviar mensaje';
                                if (result.data.message) {
                                    errorMsg = result.data.message;
                                } else if (result.data.errors) {
                                    errorMsg = Object.values(result.data.errors).flat().join('\n');
                                }
                                console.error('Server error:', result.data);
                                alert(errorMsg);
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('Error de red al enviar mensaje. Revisa la consola para más detalles.');
                        });
                });
            }

            // Auto open chat if query param exists
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('open_chat')) {
                openTaskChat();
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endsection