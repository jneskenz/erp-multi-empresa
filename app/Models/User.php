<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Verificar si el usuario es superadministrador
     * Validación segura: debe estar en la lista hardcodeada Y tener el campo en true
     */
    public function isSuperAdmin(): bool
    {
        // 1. Verificar que el email esté en la lista de configuración
        $allowedEmails = config('superadmin.allowed_emails', []);
        
        if (!in_array($this->email, $allowedEmails)) {
            // Log de intento de acceso no autorizado
            if ($this->is_super_admin === true) {
                Log::warning('Intento de acceso superadmin no autorizado', [
                    'user_id' => $this->id,
                    'email' => $this->email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
            return false;
        }

        // 2. Verificar que tenga el campo is_super_admin en true
        if ($this->is_super_admin !== true) {
            return false;
        }

        // 3. Verificación adicional: email verificado (si está habilitado)
        if (config('superadmin.require_email_verification', true) && !$this->hasVerifiedEmail()) {
            return false;
        }

        // 4. Log de acceso exitoso de superadmin
        if (config('superadmin.log_superadmin_access', true)) {
            Log::info('Acceso de superadministrador', [
                'user_id' => $this->id,
                'email' => $this->email,
                'ip' => request()->ip(),
            ]);
        }

        return true;
    }

    /**
     * Override del método can para superadministradores
     */
    public function can($abilities, $arguments = [])
    {
        // Si es superadministrador, puede hacer todo
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Si no es superadministrador, usar la lógica normal de Spatie
        return parent::can($abilities, $arguments);
    }

    /**
     * Relación con las personalizaciones del usuario
     */
    public function customization()
    {
        return $this->hasOne(UserCustomization::class);
    }

    /**
     * Obtener o crear las personalizaciones del usuario
     */
    public function getCustomization()
    {

        if (!$this->customization) {
            $this->customization()->create(UserCustomization::getDefaults());
            $this->load('customization');
        }
        
        return $this->customization;
    }
}
