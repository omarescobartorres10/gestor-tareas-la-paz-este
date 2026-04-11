@extends('layouts.app')

@section('title', 'Tareas Archivadas')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <i class="fas fa-archive text-gray-600 mr-2"></i>
                            Tareas Archivadas
                        </h1>
                        <p class="text-gray-600 mt-1">{{ $tasks->total() }} tareas archivadas</p>
                    </div>
                    <a href="{{ route('tasks.index') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a Tareas
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('tasks.archived') }}">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar tareas archivadas..."
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Archived Tasks Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($tasks as $task)
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow opacity-75">
                        <!-- Priority Indicator -->
                        <div class="h-1.5 rounded-t-lg
                                @if($task->priority === 'Alta') bg-red-500
                                @elseif($task->priority === 'Media') bg-amber-500
                                @else bg-green-500
                                @endif">
                        </div>

                        <!-- Card Content -->
                        <div class="p-5">
                            <!-- Archived Badge -->
                            <div class="mb-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300">
                                    <i class="fas fa-archive mr-1"></i>
                                    Archivada {{ $task->archived_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Title -->
                            <a href="{{ route('tasks.show', $task) }}"
                                class="block text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors mb-3 line-clamp-2">
                                {{ $task->title }}
                            </a>

                            <!-- Description -->
                            @if($task->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $task->description }}</p>
                            @endif

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <!-- Priority Badge -->
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold
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
                                        @else bg-gray-50 text-gray-700 border border-gray-200
                                        @endif">
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
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="flex-1 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-center text-sm transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>

                                @if(auth()->user()->id === $task->creator_id || auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('tasks.unarchive', $task) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('¿Restaurar esta tarea?')"
                                            class="w-full px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 font-semibold rounded-lg text-sm transition-colors">
                                            <i class="fas fa-undo mr-1"></i> Restaurar
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Archived Date -->
                            <div class="mt-3 text-xs text-gray-500 text-center">
                                Archivada el {{ $task->archived_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="col-span-full">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                            <i class="fas fa-archive text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay tareas archivadas</h3>
                            <p class="text-gray-600 mb-6">Las tareas archivadas aparecerán aquí</p>
                            <a href="{{ route('tasks.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver a Tareas
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

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection