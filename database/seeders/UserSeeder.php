<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de roles/usuarios con formato nombre@departamento
        $roles = [
            'adela@Comunicaciones' => 'Comunicaciones',
            'alejandra@gerente' => 'gerente',
            'alexis@proyectos' => 'proyectos',
            'ana@sublegal' => 'sublegal',
            'andres@construcciones' => 'construcciones',
            'armando@cementerios' => 'cementerios',
            'azucena@talentohumano' => 'talentohumano',
            'baltazar@cam' => 'cam',
            'beatriz@sjn' => 'sjn',
            'boanerges@contravencional' => 'contravencional',
            'brenda@asistentemercado' => 'asistentemercado',
            'calero@financiera' => 'financiera',
            'carlar@mercado' => 'mercado',
            'carlos@activofijo' => 'activofijo',
            'carlos@proyectos' => 'proyectos',
            'carlos@sjn' => 'sjn',
            'claudia@empleabilidad' => 'empleabilidad',
            'cristian@turismo' => 'turismo',
            'daniel@asistenteagricultura' => 'asistenteagricultura',
            'donovan@proteccioncivil' => 'proteccioncivil',
            'eduardo@asistentedeporte' => 'asistentedeporte',
            'eduardo@asistentesjn' => 'asistentesjn',
            'eliseo@juventud' => 'juventud',
            'emerson@asistentetransporte' => 'asistentetransporte',
            'esmeralda@subgerencia' => 'subgerencia',
            'estefany@asistentesubgerencia' => 'asistentesubgerencia',
            'evelin@asistenteproteccioncivil' => 'asistenteproteccioncivil',
            'fatima@ambiental' => 'ambiental',
            'fatima@ucp' => 'ucp',
            'fernando@sro' => 'sro',
            'francisco@serviciosgenerales' => 'serviciosgenerales',
            'gabriel@asistenteinnovacion' => 'asistenteinnovacion',
            'gerson@transporte' => 'transporte',
            'gloria@social' => 'social',
            'henry@cooperacion' => 'cooperacion',
            'hugo@asistentelimpieza' => 'asistentelimpieza',
            'jerardina@ref' => 'ref',
            'jerson@innovacion' => 'innovacion',
            'jorge@legal' => 'legal',
            'karen@asistenteserviciosgenerales' => 'asistenteserviciosgenerales',
            'karla@empleabilidad' => 'empleabilidad',
            'katia@ucp' => 'ucp',
            'kevin@ambiental' => 'ambiental',
            'kevin@asistentefinanciera' => 'asistentefinanciera',
            'mallumy@niñez' => 'niñez',
            'marcela@alcaldesa' => 'alcaldesa',
            'marcela@sindica' => 'sindica',
            'marco@limpieza' => 'limpieza',
            'marisol@social' => 'social',
            'martinez@asistentealumbrado' => 'asistentealumbrado',
            'monge@tesoreria' => 'tesoreria',
            'nancy@asistentecam' => 'asistentecam',
            'oscar@asistenteactivofijo' => 'asistenteactivofijo',
            'oscar@infraestructura' => 'infraestructura',
            'otsmaro@inversion' => 'inversion',
            'pamela@contravencional' => 'contravencional',
            'pamela@sro' => 'sro',
            'patricia@asistenteinfraestructura' => 'asistenteinfraestructura',
            'raul@deportes' => 'deportes',
            'raul@transporte' => 'transporte',
            'rivas@alumbrado' => 'alumbrado',
            'roberto@agricultura' => 'agricultura',
            'ronald@catastro' => 'catastro',
            'sandra@comunicaciones' => 'comunicaciones',
            'sofia@comunicaciones' => 'comunicaciones',
            'walter@proyectos' => 'proyectos',
            'wilver@tributaria' => 'tributaria',
            'xiomara@asistentetalentohumano' => 'asistentetalentohumano',
            'yoselyn@asistenteinversion' => 'asistenteinversion',
        ];

        // Usuarios con privilegios especiales para asignar tareas
        $privilegedUsers = [
            'marcela@alcaldesa',
            'marcela@sindica',
            'alejandra@gerente',
            'otsmaro@inversion',
            'yoselyn@asistenteinversion',
            'henry@cooperacion',
            'jorge@legal',
            'gerson@transporte',
            'azucena@talentohumano',
            'jerson@innovacion',
            'francisco@serviciosgenerales',
            'donovan@proteccioncivil',
            'sofia@comunicaciones',
            'carlos@sjn',
            'fernando@sro',
            'marisol@social',
            'beatriz@sjn',
            'esmeralda@subgerencia',
            'estefany@asistentesubgerencia',
        ];

        // Contraseñas predefinidas para algunos usuarios
        $predefinedPasswords = [
            'otsmaro@gerente' => 'clave1',
            'yoselyn@asistente' => 'clave2',
            'fatima@ambiental' => 'clave3',
            'kevin@ambiental' => 'clave4',
            'claudia@empleabilidad' => 'clave5',
            'karla@empleabilidad' => 'clave6',
            'walter@proyectos' => 'clave7',
            'alexis@proyectos' => 'clave8',
            'carlos@proyectos' => 'clave9',
            'andres@construcciones' => 'clave10',
        ];

        // Crear usuario admin principal
        User::updateOrCreate(
            ['email' => 'admin@taskflow.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@taskflow.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'Administración',
                'position' => 'Administrador del Sistema',
                'is_active' => true,
                'can_assign_tasks' => true,
            ]
        );

        // Crear todos los usuarios del sistema
        foreach ($roles as $email => $department) {
            // Extraer nombre del email (parte antes del @)
            $nombre = ucfirst(explode('@', $email)[0]);

            // Determinar si tiene privilegios
            $canAssign = in_array($email, $privilegedUsers);

            // Determinar la contraseña
            $password = isset($predefinedPasswords[$email])
                ? $predefinedPasswords[$email]
                : 'password'; // Contraseña por defecto

            // Determinar el rol (admin para usuarios privilegiados, usuario para el resto)
            $role = $canAssign ? 'admin' : 'usuario';

            // Agregar dominio al email para que sea válido
            $validEmail = $email . '@lapaz.gob.bo';

            // Crear o actualizar usuario
            User::updateOrCreate(
                ['email' => $validEmail],
                [
                    'name' => $nombre,
                    'email' => $validEmail,
                    'password' => Hash::make($password),
                    'role' => $role,
                    'department' => ucfirst($department),
                    'position' => ucfirst($department),
                    'is_active' => true,
                    'can_assign_tasks' => $canAssign,
                ]
            );
        }

        $this->command->info('✅ Usuarios creados exitosamente!');
        $this->command->info('Total de usuarios: ' . (count($roles) + 1));
        $this->command->info('Usuarios con privilegios de asignación: ' . count($privilegedUsers));
    }
}
