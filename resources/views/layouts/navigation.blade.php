<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('tasks.index') }}" class="flex items-center gap-2 sm:gap-3">
                        <div
                            class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-tasks text-white text-base sm:text-lg"></i>
                        </div>
                        <span class="hidden sm:inline text-xl font-bold text-gray-900">Gestor de Tareas</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                        <i class="fas fa-list mr-2"></i>
                        {{ __('Tareas') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tasks.archived')" :active="request()->routeIs('tasks.archived')">
                        <i class="fas fa-archive mr-2"></i>
                        {{ __('Archivadas') }}
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ __('Panel Admin') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Side: Profile + Notifications -->
            <div class="hidden sm:flex sm:items-center sm:gap-4">
                <!-- Settings Dropdown (Profile) -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left">
                                    <div class="font-semibold text-gray-700">{{ Auth::user()->name }}</div>
                                </div>
                            </div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fas fa-user-cog mr-2"></i>
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Email Notifications Toggle -->
                        <form method="POST" action="{{ route('notifications.toggle-email') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between">
                                <span>
                                    <i
                                        class="fas fa-envelope mr-2 {{ auth()->user()->email_notifications ? 'text-green-500' : 'text-gray-400' }}"></i>
                                    Notif. Correo
                                </span>
                                @if(auth()->user()->email_notifications)
                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">ON</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-full">OFF</span>
                                @endif
                            </button>
                        </form>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                <!-- Notification Bell -->
                <div class="relative" x-data="{ openNotifications: false }">
                    <button @click="openNotifications = !openNotifications"
                        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>

                        @if(auth()->user()->unreadNotificationsCount() > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                {{ auth()->user()->unreadNotificationsCount() }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div x-show="openNotifications" @click.away="openNotifications = false" x-transition
                        class="absolute right-0 mt-2 w-96 lg:w-[28rem] bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                        style="display: none; min-width: 384px;">

                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Notificaciones</h3>

                            @if(auth()->user()->unreadNotificationsCount() > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                                        Marcar todas como leídas
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Lista de notificaciones -->
                        <div class="max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->take(10)->get() as $notification)
                                <a href="{{ route('notifications.read', ['notification' => $notification]) }}"
                                    class="block p-3 hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }} border-b border-gray-100 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if(!$notification->is_read)
                                            <span class="inline-block w-2 h-2 bg-blue-600 rounded-full ml-2 mt-1"></span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-4xl mb-2"></i>
                                    <p class="text-sm">No hay notificaciones</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer -->
                        <div class="p-3 border-t border-gray-200 text-center">
                            <a href="{{ route('notifications.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                                Ver todas las notificaciones
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Theme Switcher -->
                <div class="relative" x-data="{ openTheme: false }">
                    <button @click="openTheme = !openTheme"
                        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
                        title="Ajustar colores">
                        <i class="fas fa-adjust text-xl"></i>
                    </button>

                    <!-- Theme Dropdown -->
                    <div x-show="openTheme" @click.away="openTheme = false" x-transition
                        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50 p-4"
                        style="display: none;">

                        <!-- Color Saturation Slider -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                                <i class="fas fa-palette mr-1"></i> Intensidad de Colores
                            </label>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 text-xs">Suave</span>
                                <input type="range" id="saturation-slider" min="0" max="100" value="100"
                                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                    oninput="setSaturation(this.value)">
                                <span class="text-gray-400 text-xs">Vivo</span>
                            </div>
                            <div class="text-center mt-1">
                                <span id="saturation-value" class="text-xs text-gray-500">100%</span>
                            </div>
                        </div>

                        <!-- Predefined Themes -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                                <i class="fas fa-swatchbook mr-1"></i> Tema Visual
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px;">
                                <!-- Corporativo (Default) -->
                                <button type="button" onclick="setTheme('corporativo')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="corporativo" title="Corporativo (Azul)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Corporativo</span>
                                </button>
                                <!-- Natural -->
                                <button type="button" onclick="setTheme('natural')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="natural" title="Natural (Verde)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #059669, #047857); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Natural</span>
                                </button>
                                <!-- Elegante -->
                                <button type="button" onclick="setTheme('elegante')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="elegante" title="Elegante (Púrpura)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Elegante</span>
                                </button>
                                <!-- Cálido -->
                                <button type="button" onclick="setTheme('calido')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="calido" title="Cálido (Naranja)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #ea580c, #c2410c); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Cálido</span>
                                </button>
                                <!-- Océano -->
                                <button type="button" onclick="setTheme('oceano')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="oceano" title="Océano (Cyan)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #0891b2, #0e7490); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Océano</span>
                                </button>
                                <!-- Corporativo 2 -->
                                <button type="button" onclick="setTheme('corp2')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left col-span-2"
                                    data-theme-name="corp2" title="Corporativo 2 (Azul Profundo)">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #191b53, #02042a); border-radius: 3px;"></span>
                                        <span
                                            style="width: 16px; height: 16px; background: #1881f0; border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Corporativo 2</span>
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-3">

                        <!-- Dark Mode Toggle -->
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-700 flex items-center gap-2">
                                <i class="fas fa-moon text-indigo-500"></i>
                                Modo Oscuro
                            </label>
                            <button id="dark-mode-toggle" onclick="toggleDarkMode()"
                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                                <span id="dark-mode-knob"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-transform translate-x-1"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile: Theme, Notifications, Hamburger -->
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                <!-- Mobile Theme Switcher -->
                <div class="relative" x-data="{ openMobileTheme: false }">
                    <button @click="openMobileTheme = !openMobileTheme"
                        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
                        title="Ajustar colores">
                        <i class="fas fa-adjust text-lg"></i>
                    </button>

                    <!-- Mobile Theme Dropdown -->
                    <div x-show="openMobileTheme" @click.away="openMobileTheme = false" x-transition
                        class="fixed right-4 top-14 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50 p-4"
                        style="display: none;">

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                                <i class="fas fa-palette mr-1"></i> Intensidad de Colores
                            </label>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 text-xs">Suave</span>
                                <input type="range" id="mobile-saturation-slider" min="0" max="100" value="100"
                                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                    oninput="setSaturation(this.value); document.getElementById('saturation-slider').value = this.value;">
                                <span class="text-gray-400 text-xs">Vivo</span>
                            </div>
                        </div>

                        <!-- Predefined Themes (Mobile) -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">
                                <i class="fas fa-swatchbook mr-1"></i> Tema Visual
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px;">
                                <button type="button" onclick="setTheme('corporativo')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="corporativo">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;"><span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Corporativo</span>
                                </button>
                                <button type="button" onclick="setTheme('natural')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="natural">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;"><span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #059669, #047857); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Natural</span>
                                </button>
                                <button type="button" onclick="setTheme('elegante')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="elegante">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;"><span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Elegante</span>
                                </button>
                                <button type="button" onclick="setTheme('calido')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="calido">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;"><span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #ea580c, #c2410c); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Cálido</span>
                                </button>
                                <button type="button" onclick="setTheme('oceano')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left"
                                    data-theme-name="oceano">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;"><span
                                            style="width: 16px; height: 16px; background: linear-gradient(135deg, #0891b2, #0e7490); border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Océano</span>
                                </button>
                                <button type="button" onclick="setTheme('corp2')"
                                    class="theme-card p-2 rounded-lg border-2 border-gray-200 hover:border-gray-400 transition-all text-left col-span-2"
                                    data-theme-name="corp2">
                                    <div style="display: flex; gap: 3px; margin-bottom: 3px;">
                                        <span style="width: 16px; height: 16px; background: linear-gradient(135deg, #191b53, #02042a); border-radius: 3px;"></span>
                                        <span style="width: 16px; height: 16px; background: #1881f0; border-radius: 3px;"></span>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Corporativo 2</span>
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-3">

                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-700 flex items-center gap-2">
                                <i class="fas fa-moon text-indigo-500"></i>
                                Modo Oscuro
                            </label>
                            <button onclick="toggleDarkMode()"
                                class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 transition-colors focus:outline-none"
                                id="mobile-dark-toggle">
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition-transform translate-x-1"
                                    id="mobile-dark-knob"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Notification Bell -->
                <div class="relative" x-data="{ openMobileNotif: false }">
                    <button @click="openMobileNotif = !openMobileNotif"
                        class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bell text-lg"></i>
                        @if(auth()->user()->unreadNotificationsCount() > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                {{ auth()->user()->unreadNotificationsCount() }}
                            </span>
                        @endif
                    </button>

                    <!-- Mobile Notification Dropdown -->
                    <div x-show="openMobileNotif" @click.away="openMobileNotif = false" x-transition
                        class="fixed inset-x-4 top-16 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                        style="display: none;">
                        <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 text-sm">Notificaciones</h3>
                            @if(auth()->user()->unreadNotificationsCount() > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                                        Marcar leídas
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                                <a href="{{ route('notifications.read', ['notification' => $notification]) }}"
                                    class="block p-3 hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }} border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $notification->message }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </a>
                            @empty
                                <div class="p-6 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                    <p class="text-sm">Sin notificaciones</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-2 border-t border-gray-200 text-center">
                            <a href="{{ route('notifications.index') }}"
                                class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                                Ver todas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hamburger -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                <i class="fas fa-list mr-2"></i>
                {{ __('Tareas') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tasks.archived')" :active="request()->routeIs('tasks.archived')">
                <i class="fas fa-archive mr-2"></i>
                {{ __('Archivadas') }}
            </x-responsive-nav-link>

            @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    <i class="fas fa-chart-line mr-2"></i>
                    {{ __('Panel Admin') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user-cog mr-2"></i>
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>