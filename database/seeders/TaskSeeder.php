<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', true)->get();

        if ($users->count() < 2) {
            $this->command->error('Se necesitan al menos 2 usuarios activos. Ejecuta primero UserSeeder.');
            return;
        }

        $this->command->info('Creando tareas de ejemplo...');

        // Tareas de diferentes departamentos y prioridades
        $taskTemplates = [
            // Administración
            [
                'title' => 'Actualizar inventario de oficina',
                'description' => 'Realizar conteo físico del inventario de suministros de oficina y actualizar el sistema.',
                'priority' => 'Media',
                'status' => 'Completada',
                'days_ago_start' => 15,
                'days_until_due' => -5,
            ],
            [
                'title' => 'Preparar informe mensual de gastos',
                'description' => 'Compilar todos los gastos del mes anterior y generar reporte para la dirección.',
                'priority' => 'Alta',
                'status' => 'En progreso',
                'days_ago_start' => 5,
                'days_until_due' => 2,
            ],
            [
                'title' => 'Organizar evento de fin de año',
                'description' => 'Planificar y coordinar el evento anual de la alcaldía. Incluye reserva de local, catering y programa.',
                'priority' => 'Alta',
                'status' => 'Pendiente',
                'days_ago_start' => 2,
                'days_until_due' => 30,
            ],

            // Desarrollo/TI
            [
                'title' => 'Migrar base de datos a servidor nuevo',
                'description' => 'Realizar migración completa de la base de datos al nuevo servidor con mínimo downtime.',
                'priority' => 'Alta',
                'status' => 'Completada',
                'days_ago_start' => 20,
                'days_until_due' => -10,
            ],
            [
                'title' => 'Implementar sistema de respaldos automáticos',
                'description' => 'Configurar sistema de backups automáticos diarios con retención de 30 días.',
                'priority' => 'Alta',
                'status' => 'En progreso',
                'days_ago_start' => 7,
                'days_until_due' => 5,
            ],
            [
                'title' => 'Actualizar certificados SSL',
                'description' => 'Renovar certificados SSL de todos los dominios antes de su vencimiento.',
                'priority' => 'Media',
                'status' => 'Pendiente',
                'days_ago_start' => 1,
                'days_until_due' => 15,
            ],
            [
                'title' => 'Optimizar rendimiento del portal web',
                'description' => 'Analizar y mejorar tiempos de carga del portal ciudadano. Meta: reducir 30%.',
                'priority' => 'Media',
                'status' => 'En progreso',
                'days_ago_start' => 10,
                'days_until_due' => 10,
            ],

            // Recursos Humanos
            [
                'title' => 'Proceso de reclutamiento - Contador',
                'description' => 'Publicar convocatoria, revisar CVs y realizar entrevistas para el puesto de contador.',
                'priority' => 'Alta',
                'status' => 'En progreso',
                'days_ago_start' => 12,
                'days_until_due' => 8,
            ],
            [
                'title' => 'Capacitación en atención al ciudadano',
                'description' => 'Organizar taller de 2 días sobre atención al ciudadano para todo el personal de ventanilla.',
                'priority' => 'Media',
                'status' => 'Pendiente',
                'days_ago_start' => 3,
                'days_until_due' => 20,
            ],
            [
                'title' => 'Actualizar manual de procedimientos',
                'description' => 'Revisar y actualizar el manual de procedimientos internos según nuevas normativas.',
                'priority' => 'Baja',
                'status' => 'Pendiente',
                'days_ago_start' => 1,
                'days_until_due' => 45,
            ],

            // Servicios Públicos
            [
                'title' => 'Reparación de luminarias Av. Principal',
                'description' => 'Reemplazar 15 luminarias dañadas en la Avenida Principal entre calles 5 y 10.',
                'priority' => 'Alta',
                'status' => 'Completada',
                'days_ago_start' => 8,
                'days_until_due' => -2,
            ],
            [
                'title' => 'Mantenimiento de áreas verdes Parque Central',
                'description' => 'Poda de árboles, corte de césped y limpieza general del Parque Central.',
                'priority' => 'Media',
                'status' => 'En progreso',
                'days_ago_start' => 4,
                'days_until_due' => 3,
            ],
            [
                'title' => 'Inspección de alcantarillado Zona Sur',
                'description' => 'Realizar inspección técnica del sistema de alcantarillado en la Zona Sur tras reportes de mal olor.',
                'priority' => 'Alta',
                'status' => 'Pendiente',
                'days_ago_start' => 1,
                'days_until_due' => 7,
            ],

            // Obras Públicas
            [
                'title' => 'Bacheo Calle Comercio',
                'description' => 'Reparación de baches en Calle Comercio entre Av. 6 de Agosto y Av. Libertador.',
                'priority' => 'Alta',
                'status' => 'En progreso',
                'days_ago_start' => 6,
                'days_until_due' => 4,
            ],
            [
                'title' => 'Construcción de aceras Zona Norte',
                'description' => 'Proyecto de construcción de 500m de aceras en calles de la Zona Norte.',
                'priority' => 'Media',
                'status' => 'Pendiente',
                'days_ago_start' => 2,
                'days_until_due' => 60,
            ],
            [
                'title' => 'Estudio de factibilidad puente peatonal',
                'description' => 'Realizar estudio técnico y de factibilidad para construcción de puente peatonal en Av. Principal.',
                'priority' => 'Baja',
                'status' => 'Pendiente',
                'days_ago_start' => 1,
                'days_until_due' => 90,
            ],

            // Atención Ciudadana
            [
                'title' => 'Implementar sistema de turnos digital',
                'description' => 'Instalar y configurar sistema de turnos digital para mejorar atención en ventanillas.',
                'priority' => 'Media',
                'status' => 'Completada',
                'days_ago_start' => 25,
                'days_until_due' => -15,
            ],
            [
                'title' => 'Campaña de difusión trámites en línea',
                'description' => 'Crear material informativo y realizar campaña en redes sobre trámites disponibles en línea.',
                'priority' => 'Media',
                'status' => 'En progreso',
                'days_ago_start' => 8,
                'days_until_due' => 12,
            ],
            [
                'title' => 'Atender reclamos pendientes',
                'description' => 'Revisar y dar respuesta a los 23 reclamos ciudadanos pendientes del mes anterior.',
                'priority' => 'Alta',
                'status' => 'En progreso',
                'days_ago_start' => 3,
                'days_until_due' => 2,
            ],

            // Tareas vencidas (para análisis)
            [
                'title' => 'Revisión de contratos proveedores',
                'description' => 'Revisar y renovar contratos con proveedores que vencen este trimestre.',
                'priority' => 'Alta',
                'status' => 'Pendiente',
                'days_ago_start' => 30,
                'days_until_due' => -5,
            ],
            [
                'title' => 'Auditoría interna Q4',
                'description' => 'Realizar auditoría interna del cuarto trimestre según cronograma anual.',
                'priority' => 'Alta',
                'status' => 'Pendiente',
                'days_ago_start' => 20,
                'days_until_due' => -3,
            ],

            // Tareas de baja prioridad
            [
                'title' => 'Actualizar directorio telefónico',
                'description' => 'Actualizar el directorio telefónico interno con los nuevos números y extensiones.',
                'priority' => 'Baja',
                'status' => 'Pendiente',
                'days_ago_start' => 5,
                'days_until_due' => 30,
            ],
            [
                'title' => 'Organizar archivo físico',
                'description' => 'Reorganizar el archivo físico de documentos según nuevo sistema de clasificación.',
                'priority' => 'Baja',
                'status' => 'Pendiente',
                'days_ago_start' => 2,
                'days_until_due' => 60,
            ],
            [
                'title' => 'Inventario de equipos de cómputo',
                'description' => 'Realizar inventario completo de todos los equipos de cómputo y periféricos.',
                'priority' => 'Baja',
                'status' => 'Completada',
                'days_ago_start' => 40,
                'days_until_due' => -20,
            ],
        ];

        $createdTasks = [];

        foreach ($taskTemplates as $template) {
            $creator = $users->random();
            $assignee = $users->where('id', '!=', $creator->id)->random();

            $startDate = Carbon::now()->subDays($template['days_ago_start']);
            $dueDate = Carbon::now()->addDays($template['days_until_due']);

            $task = Task::create([
                'title' => $template['title'],
                'description' => $template['description'],
                'creator_id' => $creator->id,
                'assignee_id' => $assignee->id,
                'status' => $template['status'],
                'priority' => $template['priority'],
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'created_at' => $startDate,
                'updated_at' => $template['status'] === 'Completada'
                    ? $dueDate->copy()->subDays(rand(1, 5))
                    : Carbon::now(),
            ]);

            $createdTasks[] = $task;

            // Agregar comentarios a algunas tareas
            if (rand(1, 100) > 40) { // 60% de probabilidad
                $this->addCommentsToTask($task, $users);
            }
        }

        $this->command->info('✓ ' . count($createdTasks) . ' tareas creadas exitosamente');

        // Estadísticas
        $this->command->info('');
        $this->command->info('Estadísticas:');
        $this->command->info('- Completadas: ' . Task::where('status', 'Completada')->count());
        $this->command->info('- En progreso: ' . Task::where('status', 'En progreso')->count());
        $this->command->info('- Pendientes: ' . Task::where('status', 'Pendiente')->count());
        $this->command->info('- Alta prioridad: ' . Task::where('priority', 'Alta')->count());
        $this->command->info('- Media prioridad: ' . Task::where('priority', 'Media')->count());
        $this->command->info('- Baja prioridad: ' . Task::where('priority', 'Baja')->count());
    }

    private function addCommentsToTask(Task $task, $users)
    {
        $commentTemplates = [
            '✅ Tarea iniciada. Comenzando con la planificación.',
            '📝 Avance del 30%. Todo según lo previsto.',
            '⚠️ Encontramos un pequeño inconveniente, pero ya está resuelto.',
            '💬 Necesitamos coordinar con el departamento de {dept} para continuar.',
            '📊 Actualización: hemos completado la primera fase.',
            '🔄 Revisando los últimos detalles antes de finalizar.',
            '✨ Excelente trabajo del equipo. Vamos por buen camino.',
            '📞 Coordiné con el proveedor, confirman entrega para mañana.',
            '⏰ Recordatorio: la fecha límite se acerca.',
            '🎯 Objetivo cumplido. Procediendo con la siguiente etapa.',
        ];

        $departments = ['Administración', 'Finanzas', 'Recursos Humanos', 'Servicios'];

        $numComments = rand(1, 4);

        for ($i = 0; $i < $numComments; $i++) {
            $commentText = $commentTemplates[array_rand($commentTemplates)];
            $commentText = str_replace('{dept}', $departments[array_rand($departments)], $commentText);

            $commentDate = Carbon::parse($task->created_at)->addDays(rand(1, 10));

            Comment::create([
                'task_id' => $task->id,
                'user_id' => $users->random()->id,
                'content' => $commentText,
                'created_at' => $commentDate,
                'updated_at' => $commentDate,
            ]);
        }
    }
}
