@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-bell text-gray-600 mr-2"></i>
                        Notificaciones
                    </h1>
                    <p class="text-gray-600 mt-1">{{ $notifications->total() }} notificaciones en total</p>
                </div>

                @if($notifications->where('is_read', false)->count() > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Marcar todas como leídas
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="space-y-3">
                @forelse($notifications as $notification)
                    <div
                        class="bg-white rounded-lg border {{ $notification->is_read ? 'border-gray-200' : 'border-blue-200 bg-blue-50' }} overflow-hidden">
                        <a href="{{ route('notifications.read', ['notification' => $notification]) }}"
                            class="block p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Type Icon -->
                                    <div class="flex items-center gap-3 mb-2">
                                        @if($notification->type === 'mention')
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-at text-blue-600"></i>
                                            </div>
                                        @elseif($notification->type === 'assigned')
                                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-user-plus text-green-600"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-bell text-gray-600"></i>
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $notification->message }}</p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                En: <span class="font-medium">{{ $notification->task->title }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Timestamp -->
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-2">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                <!-- Unread Indicator -->
                                @if(!$notification->is_read)
                                    <div class="ml-4">
                                        <span class="inline-block w-3 h-3 bg-blue-600 rounded-full"></span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                        <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes notificaciones</h3>
                        <p class="text-gray-600 mb-6">Las notificaciones aparecerán aquí cuando te mencionen en tareas</p>
                        <a href="{{ route('tasks.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver a Tareas
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection