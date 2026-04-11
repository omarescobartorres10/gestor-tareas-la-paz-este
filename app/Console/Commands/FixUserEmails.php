<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige correos electrónicos mal formados (ej: multiples arrobas)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $email = $user->email;

            // Cuenta cuantas arrobas tiene
            if (substr_count($email, '@') > 1) {
                // Estrategia: Reemplazar todas las arrobas MENOS la última por un punto
                // Ejemplo: fatima@ambiental@lapaz.gob.bo -> fatima.ambiental@lapaz.gob.bo

                $lastAtPos = strrpos($email, '@');
                $localPart = substr($email, 0, $lastAtPos);
                $domainPart = substr($email, $lastAtPos); // Incluye la arroba final

                // Reemplazar @ por . en la parte local
                $newLocalPart = str_replace('@', '.', $localPart);

                $newEmail = $newLocalPart . $domainPart;

                $this->info("Corrigiendo: {$email} -> {$newEmail}");

                $user->email = $newEmail;
                $user->save();
                $count++;
            }
        }

        $this->info("Se corrigieron {$count} usuarios.");
    }
}
