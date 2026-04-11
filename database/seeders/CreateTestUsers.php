<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Seeder
{
    /**
     * Crea usuarios de prueba con contraseñas ÚNICAS y SEGURAS.
     */
    public function run(): void
    {
        $this->command->info('🔐 Creando usuarios de prueba con contraseñas seguras...');

        // Admin principal
        User::updateOrCreate(
            ['email' => 'admin@taskflow.com'],
            [
                'name' => 'Administrador Sistema',
                'password' => Hash::make('Admin$2025!Seguro'),
                'role' => 'admin',
                'department' => 'Administración',
                'position' => 'Administrador del Sistema',
                'is_active' => true,
                'can_assign_tasks' => true,
            ]
        );

        // Usuario con privilegios de asignación
        User::updateOrCreate(
            ['email' => 'gestor@lapaz.gob.bo'],
            [
                'name' => 'Gestor Tareas',
                'password' => Hash::make('Gestor$2025!Test'),
                'role' => 'usuario',
                'department' => 'Gestión',
                'position' => 'Gestor de Tareas',
                'is_active' => true,
                'can_assign_tasks' => true,
            ]
        );

        // Usuario colaborador normal
        User::updateOrCreate(
            ['email' => 'colaborador@lapaz.gob.bo'],
            [
                'name' => 'Colaborador Prueba',
                'password' => Hash::make('Colaborador$2025!Test'),
                'role' => 'usuario',
                'department' => 'Operaciones',
                'position' => 'Colaborador',
                'is_active' => true,
                'can_assign_tasks' => false,
            ]
        );

        $this->command->info('✅ Usuarios de prueba creados:');
        $this->command->line('   🔑 admin@taskflow.com');
        $this->command->line('   👤 gestor@lapaz.gob.bo');
        $this->command->line('   👤 colaborador@lapaz.gob.bo');
        $this->command->warn('⚠️  Guardá las contraseñas que definiste en el seeder. No las subas al repo.');
    }
}
