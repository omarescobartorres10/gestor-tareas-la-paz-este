<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetAppData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra todas las tareas, comentarios y notificaciones, pero mantiene los usuarios intactos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('¿Estás seguro? Esto borrará TODAS las tareas, comentarios y archivos adjuntos. Los usuarios NO se borrarán.')) {
            return;
        }

        $this->info('Limpiando base de datos...');

        Schema::disableForeignKeyConstraints();

        // Lista de tablas a vaciar (excepto users y migrations)
        // Basado en tus migraciones
        $tables = [
            'notifications',
            'chat_attachments',
            'comment_user', // Pivot si existe
            'comments',
            'task_user',    // Pivot de asignaciones
            'tasks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("Tabla '$table' vaciada.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('¡Listo! El sistema está limpio para pruebas, pero tus usuarios siguen ahí.');
    }
}
