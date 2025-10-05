<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckSuperAdminSecurity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:superadmin-security';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la seguridad del sistema de superadministradores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando seguridad del sistema de superadministradores...');
        $this->newLine();

        $allowedEmails = config('superadmin.allowed_emails', []);
        $usersWithSuperAdminFlag = User::where('is_super_admin', true)->get();

        $this->info('📋 Emails permitidos en config/superadmin.php:');
        foreach ($allowedEmails as $email) {
            $this->line("  ✅ {$email}");
        }
        $this->newLine();

        $this->info('👥 Usuarios con flag is_super_admin = true:');
        $securityIssues = [];

        foreach ($usersWithSuperAdminFlag as $user) {
            $isAllowed = in_array($user->email, $allowedEmails);
            $isActualSuperAdmin = $user->isSuperAdmin();
            
            if ($isAllowed && $isActualSuperAdmin) {
                $this->line("  ✅ {$user->email} - VÁLIDO (en lista y activo)");
            } elseif ($isAllowed && !$isActualSuperAdmin) {
                $this->line("  ⚠️  {$user->email} - EN LISTA pero no pasa validación completa");
            } elseif (!$isAllowed && $isActualSuperAdmin) {
                $this->error("  ❌ {$user->email} - RIESGO DE SEGURIDAD: No está en lista permitida pero es superadmin");
                $securityIssues[] = $user;
            } else {
                $this->error("  ❌ {$user->email} - BLOQUEADO: Tiene flag pero no está permitido");
                $securityIssues[] = $user;
            }
        }

        $this->newLine();

        if (empty($securityIssues)) {
            $this->info('🛡️  SEGURIDAD OK: No se encontraron problemas de seguridad.');
        } else {
            $this->error('⚠️  ALERTA DE SEGURIDAD: Se encontraron ' . count($securityIssues) . ' problema(s):');
            
            foreach ($securityIssues as $user) {
                $this->error("   - Usuario ID {$user->id}: {$user->email}");
            }
            
            $this->newLine();
            $this->warn('🔧 Para solucionarlo:');
            $this->warn('   1. Agregar emails legítimos a config/superadmin.php');
            $this->warn('   2. O ejecutar: php artisan make:superadmin email@example.com --revoke');
        }

        $this->newLine();
        $this->info('🔒 Validaciones de seguridad activas:');
        $this->line('   ✅ Lista de emails hardcodeada en configuración');
        $this->line('   ✅ Doble validación (flag + lista)');
        $this->line('   ✅ Logs de intentos no autorizados');
        $this->line('   ✅ Verificación de email: ' . (config('superadmin.require_email_verification') ? 'Habilitada' : 'Deshabilitada'));

        return empty($securityIssues) ? 0 : 1;
    }
}
