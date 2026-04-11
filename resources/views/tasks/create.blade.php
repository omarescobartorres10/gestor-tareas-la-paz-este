@extends('layouts.app')

@section('title', 'Nueva Tarea')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Nueva Tarea</h1>
                <p class="text-gray-600">Crea y asigna una nueva tarea al equipo</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-8">
                    <form method="POST" action="{{ route('tasks.store') }}" id="taskForm">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Título de la Tarea *
                            </label>
                            <input type="text" name="title"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                                placeholder="Ej: Diseñar interfaz de usuario..." required>
                            @error('title')
                                <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors resize-none"
                                placeholder="Describe los detalles de la tarea..."></textarea>
                            @error('description')
                                <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Assignee -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Asignar a *
                            </label>
                            <select name="assignee_id"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                                required>
                                <option value="">Seleccionar usuario...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} @if($user->department) - {{ $user->department }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('assignee_id')
                                <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Priority and Start Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Prioridad *
                                </label>
                                <select name="priority"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                                    required>
                                    <option value="Baja">Baja</option>
                                    <option value="Media" selected>Media</option>
                                    <option value="Alta">Alta</option>
                                </select>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Fecha de Inicio *
                                </label>
                                <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                                    required>
                                @error('start_date')
                                    <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Fecha Límite *
                            </label>
                            <input type="date" name="due_date"
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-100 transition-colors"
                                required>
                            @error('due_date')
                                <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                                <i class="fas fa-save"></i>
                                <span>Crear Tarea</span>
                            </button>

                            <a href="{{ route('tasks.index') }}"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg shadow-sm border border-gray-300 transition-colors">
                                <i class="fas fa-times"></i>
                                <span>Cancelar</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Notice -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-1">Información</p>
                        <p class="text-sm text-gray-700">
                            Asigna prioridades correctamente para ayudar al equipo a organizar su trabajo.
                            Las tareas de alta prioridad aparecerán destacadas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('taskForm');

            form.addEventListener('submit', function (e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Creando...</span>';
                submitBtn.disabled = true;
            });
        });
    </script>
@endsection