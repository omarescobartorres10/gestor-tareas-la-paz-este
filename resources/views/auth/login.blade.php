<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Gestor de Tareas</title>

    <!-- Preconnect to external domains for faster loading -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg mb-4">
                    <i class="fas fa-tasks text-white text-4xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Gestor de Tareas</h2>
                <p class="text-gray-600">Sistema de Gestión de Tareas</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Iniciar Sesión</h3>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Correo Electrónico
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                            placeholder="usuario@lapaz.gob.bo" required autofocus autocomplete="username">
                        @error('email')
                            <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <input id="password" type="password" name="password"
                            class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors"
                            placeholder="••••••••" required autocomplete="current-password">
                        @error('password')
                            <span class="text-red-600 text-sm mt-1 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <span class="ml-2 text-sm text-gray-700">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-colors">
                        Iniciar Sesión
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Gestor de Tareas - Alcaldía de la Paz Este
                </p>
            </div>

            <!-- PWA Install Instructions -->
            <div class="mt-6 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-mobile-alt text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-indigo-900 text-sm mb-2">📲 Instalar Como Aplicación</h4>
                        <div class="space-y-2 text-xs text-indigo-800">
                            <div class="flex items-start gap-2">
                                <i class="fab fa-android text-green-600 mt-0.5"></i>
                                <span><strong>Android:</strong> Toca el menú (⋮) → "Añadir a pantalla de inicio"</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fab fa-apple text-gray-700 mt-0.5"></i>
                                <span><strong>iPhone:</strong> Toca <i class="fas fa-share-square text-blue-500"></i> →
                                    "Añadir a inicio"</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-indigo-600 mt-2 font-medium">
                            <i class="fas fa-info-circle"></i> Accede sin navegador, pantalla completa
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>