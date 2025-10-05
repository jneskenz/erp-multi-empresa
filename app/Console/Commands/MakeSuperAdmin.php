<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin {email} {--revoke : Revocar privilegios de superadmin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hacer a un usuario superadministrador o revocar sus privilegios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $revoke = $this->option('revoke');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario con email '{$email}' no encontrado.");
            return 1;
        }

        // Verificar que el email esté en la lista de superadmins permitidos
        $allowedEmails = config('superadmin.allowed_emails', []);
        
        if (!$revoke && !in_array($email, $allowedEmails)) {
            $this->error("❌ SEGURIDAD: Email '{$email}' no está en la lista de superadmins permitidos.");
            $this->error("   Edita config/superadmin.php para agregar este email a la lista.");
            return 1;
        }
        
        if ($revoke) {
            // Revocar privilegios de superadmin
            $user->update(['is_super_admin' => false]);
            $this->info("✅ Privilegios de superadministrador revocados para: {$user->name} ({$email})");
        } else {
            // Otorgar privilegios de superadmin
            $user->update(['is_super_admin' => true]);
            $this->info("✅ {$user->name} ({$email}) ahora es SUPERADMINISTRADOR");
            $this->warn("⚠️  Este usuario puede acceder a TODO sin restricciones de permisos.");
            $this->info("🔐 Validación de seguridad: Email verificado en config/superadmin.php");
        }
        
        return 0;
    }
}
