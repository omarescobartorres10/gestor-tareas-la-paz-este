<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FillDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:fill {--fresh : Drop all tables and migrate fresh before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Llena el sistema con datos de demostración (usuarios y tareas)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando llenado de datos de demostración...');
        $this->newLine();

        if ($this->option('fresh')) {
            if (!$this->confirm('⚠️  Esto eliminará TODOS los datos existentes. ¿Continuar?', false)) {
                $this->error('Operación cancelada.');
                return 1;
            }

            $this->warn('Eliminando datos existentes...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
            $this->info('✓ Base de datos reiniciada');
            $this->newLine();
        }

        // Ejecutar seeders
        $this->info('Creando usuarios...');
        Artisan::call('db:seed', ['--class' => 'UserSeeder'], $this->getOutput());

        $this->newLine();
        $this->info('Creando tareas...');
        Artisan::call('db:seed', ['--class' => 'TaskSeeder'], $this->getOutput());

        $this->newLine();
        $this->info('✅ Sistema llenado exitosamente con datos de demostración');
        $this->newLine();

        $this->table(
            ['Recurso', 'Cantidad'],
            [
                ['Usuarios', \App\Models\User::count()],
                ['Tareas', \App\Models\Task::count()],
                ['Comentarios', \App\Models\Comment::count()],
            ]
        );

        $this->newLine();
        $this->info('Puedes acceder al sistema con:');
        $this->line('  Email: admin@taskflow.com');
        $this->line('  Password: Admin$2025!Seguro');

        return 0;
    }
}
