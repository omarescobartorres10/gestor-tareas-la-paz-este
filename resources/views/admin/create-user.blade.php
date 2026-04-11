@extends('layouts.app')

@section('title', 'Crear Usuario - Admin')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-purple-600"></i> Crear Nuevo Usuario
            </h2>
            <p class="text-gray-600 mt-2">Añade un nuevo usuario al sistema</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('admin.store-user') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-user"></i> Nombre Completo *
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employee ID (CI) -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-id-card"></i> Cédula de Identidad
                        </label>
                        <input type="text" name="employee_id" value="{{ old('employee_id') }}" placeholder="Ej: 1234567"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Identificador único del funcionario</p>
                        @error('employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Contraseña *
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <button type="button" onclick="generatePassword()"
                                class="text-sm text-purple-600 hover:text-purple-800 font-semibold">
                                <i class="fas fa-magic"></i> Generar contraseña segura
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div id="password-strength"></div>
                        <p class="text-xs text-gray-500 mt-1">Mín. 8 caracteres: mayúscula, minúscula, número y @$!%*#?&</p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Confirmar Contraseña *
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-shield-alt"></i> Rol *
                        </label>
                        <select name="role" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('role') border-red-500 @enderror">
                            <option value="usuario" selected>Usuario</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-briefcase"></i> Posición / Cargo
                        </label>
                        <input type="text" name="position" value="{{ old('position') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('position') border-red-500 @enderror">
                        @error('position')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-building"></i> Departamento
                        </label>
                        <input type="text" name="department" value="{{ old('department') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('department') border-red-500 @enderror">
                        @error('department')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 justify-end mt-8">
                    <a href="{{ route('admin.users') }}"
                        class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition font-semibold">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition font-semibold shadow-lg transform hover:scale-105">
                        <i class="fas fa-save"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');

            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Generate secure password
        function generatePassword() {
            const length = 16;
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const special = '@$!%*#?&';
            const all = lowercase + uppercase + numbers + special;

            // Ensure at least one of each required type
            let password = '';
            password += lowercase[Math.floor(Math.random() * lowercase.length)];
            password += uppercase[Math.floor(Math.random() * uppercase.length)];
            password += numbers[Math.floor(Math.random() * numbers.length)];
            password += special[Math.floor(Math.random() * special.length)];

            // Fill the rest randomly
            for (let i = password.length; i < length; i++) {
                password += all[Math.floor(Math.random() * all.length)];
            }

            // Shuffle the password
            password = password.split('').sort(() => Math.random() - 0.5).join('');

            // Set to both fields
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;

            // Make visible
            document.getElementById('password').type = 'text';
            document.getElementById('password-eye').classList.remove('fa-eye');
            document.getElementById('password-eye').classList.add('fa-eye-slash');

            // Update strength indicator
            updatePasswordStrength(password);

            // Show success message
            showGeneratedNotice();
        }

        // Password strength indicator
        function updatePasswordStrength(password) {
            const indicator = document.getElementById('password-strength');
            if (!indicator) return;

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[@$!%*#?&]/.test(password)) strength++;

            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500', 'bg-green-600'];
            const labels = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Fuerte', 'Excelente'];

            const idx = Math.min(strength, colors.length - 1);
            indicator.innerHTML = `
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="${colors[idx]} h-full transition-all" style="width: ${(strength / 6) * 100}%"></div>
                    </div>
                    <span class="text-xs font-semibold ${colors[idx].replace('bg-', 'text-')}">${labels[idx]}</span>
                </div>
            `;
        }

        function showGeneratedNotice() {
            const notice = document.createElement('div');
            notice.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notice.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Contraseña generada - ¡Cópiala antes de enviar!';
            document.body.appendChild(notice);
            setTimeout(() => notice.remove(), 3000);
        }

        // Add event listener for password field
        document.getElementById('password')?.addEventListener('input', function () {
            updatePasswordStrength(this.value);
        });
    </script>
@endsection