<?php

namespace App\Models;

use App\Models\Workspace\Empresa;
use App\Models\Workspace\Local;
use App\Models\Workspace\Sede;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, LogsActivity;

    protected $fillable = [
        'grupo_empresa_id',
        'empresa_id',
        'sede_id',
        'local_id',
        'name',
        'email',
        'password',
        'documento_tipo',
        'documento_numero',
        'telefono',
        'fecha_nacimiento',
        'preferencias',
        'avatar',
        'activo',
        'ultimo_acceso',
        'ultimo_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fecha_nacimiento' => 'date',
        'preferencias' => 'array',
        'activo' => 'boolean',
        'ultimo_acceso' => 'datetime',
    ];

    protected $attributes = [
        'activo' => true,
    ];

    // ==================== ACTIVITY LOG ====================
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'activo', 'grupo_empresa_id', 'empresa_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ==================== RELACIONES ====================
    
    /**
     * Grupo empresarial al que pertenece
     */
    public function grupoEmpresa(): BelongsTo
    {
        return $this->belongsTo(GrupoEmpresa::class, 'grupo_empresa_id');
    }

    /**
     * Empresa principal
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Sede asignada
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Local asignado
     */
    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    /**
     * Empresas con acceso (relación múltiple)
     */
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_user')
            ->withPivot(['es_principal', 'activo', 'fecha_asignacion', 'fecha_revocacion', 'created_by'])
            ->withTimestamps();
    }

    /**
     * Empresas activas con acceso
     */
    public function empresasActivas(): BelongsToMany
    {
        return $this->empresas()->wherePivot('activo', true);
    }

    /**
     * Grupos empresariales donde es propietario
     */
    public function gruposComoPropietario(): BelongsToMany
    {
        return $this->belongsToMany(GrupoEmpresa::class, 'grupo_empresa_propietario')
            ->withPivot([
                'tipo',
                'porcentaje_participacion',
                'puede_modificar_plan',
                'puede_crear_empresas',
                'puede_asignar_usuarios',
                'fecha_desde',
                'fecha_hasta',
                'activo'
            ])
            ->withTimestamps();
    }

    /**
     * Grupos activos como propietario
     */
    public function gruposActivosComoPropietario(): BelongsToMany
    {
        return $this->gruposComoPropietario()->wherePivot('activo', true);
    }

    // ==================== SCOPES ====================
    
    /**
     * Usuarios activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Usuarios de un grupo empresarial
     */
    public function scopeDeGrupo($query, int $grupoId)
    {
        return $query->where('grupo_empresa_id', $grupoId);
    }

    /**
     * Usuarios de una empresa
     */
    public function scopeDeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Usuarios con un rol específico
     */
    public function scopeConRol($query, string $rol)
    {
        return $query->role($rol);
    }

    // ==================== MÉTODOS DE VERIFICACIÓN DE ROLES ====================
    
    /**
     * Verificar si es superusuario
     */
    public function esSuperusuario(): bool
    {
        return $this->hasRole('superusuario');
    }

    /**
     * Verificar si es propietario de algún grupo
     */
    public function esPropietario(): bool
    {
        return $this->hasRole('propietario') || 
               $this->gruposActivosComoPropietario()->exists();
    }

    /**
     * Verificar si es administrador general
     */
    public function esAdministradorGeneral(): bool
    {
        return $this->hasRole('administrador_general');
    }

    /**
     * Verificar si es propietario de un grupo específico
     */
    public function esPropietarioDeGrupo(int $grupoId): bool
    {
        return $this->gruposActivosComoPropietario()
            ->where('grupo_empresas.id', $grupoId)
            ->exists();
    }

    /**
     * Verificar si tiene acceso a un grupo empresarial
     */
    public function tieneAccesoAGrupo(int $grupoId): bool
    {
        // Superusuario tiene acceso total
        if ($this->esSuperusuario()) {
            return true;
        }
        
        // Propietario del grupo
        if ($this->esPropietarioDeGrupo($grupoId)) {
            return true;
        }
        
        // Usuario pertenece al grupo
        return $this->grupo_empresa_id === $grupoId;
    }

    /**
     * Verificar si tiene acceso a una empresa
     */
    public function tieneAccesoAEmpresa(int $empresaId): bool
    {
        // Superusuario tiene acceso total
        if ($this->esSuperusuario()) {
            return true;
        }
        
        // Empresa principal
        if ($this->empresa_id === $empresaId) {
            return true;
        }
        
        // Empresa en relación múltiple
        return $this->empresasActivas()->where('empresas.id', $empresaId)->exists();
    }

    /**
     * Verificar si puede gestionar el grupo empresarial
     */
    public function puedeGestionarGrupo(int $grupoId): bool
    {
        if ($this->esSuperusuario()) {
            return true;
        }
        
        if ($this->esAdministradorGeneral() && $this->grupo_empresa_id === $grupoId) {
            return true;
        }
        
        return $this->esPropietarioDeGrupo($grupoId);
    }

    // ==================== MÉTODOS DE CONTEXTO ====================
    
    /**
     * Obtener empresa principal del usuario
     */
    public function getEmpresaPrincipal(): ?Empresa
    {
        if ($this->empresa_id) {
            return $this->empresa;
        }
        
        // Buscar en relaciones múltiples
        return $this->empresasActivas()
            ->wherePivot('es_principal', true)
            ->first();
    }

    /**
     * Obtener todas las empresas con acceso
     * 
     * Lógica:
     * - Superusuario: todas las empresas
     * - Propietario/Admin General: todas las empresas de su grupo
     * - Usuarios operativos: solo empresas asignadas
     */
    public function getEmpresasConAcceso()
    {
        // Superusuario: todas las empresas del sistema
        if ($this->esSuperusuario()) {
            return Empresa::all();
        }
        
        // Propietario o Administrador General: todas las empresas de su grupo
        if ($this->esAdministradorGeneral() || $this->esPropietario()) {
            if ($this->grupo_empresa_id) {
                return Empresa::where('grupo_empresa_id', $this->grupo_empresa_id)->get();
            }
        }
        
        // Usuarios operativos: solo empresas asignadas
        $empresas = collect();
        
        // Empresa principal
        if ($this->empresa) {
            $empresas->push($this->empresa);
        }
        
        // Empresas adicionales de la relación many-to-many
        $this->empresasActivas->each(function ($empresa) use ($empresas) {
            if (!$empresas->contains('id', $empresa->id)) {
                $empresas->push($empresa);
            }
        });
        
        return $empresas;
    }
    
    /**
     * Obtener todas las empresas con acceso de un grupo específico
     * 
     * @param int $grupoId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEmpresasConAccesoDeGrupo(int $grupoId)
    {
        // Superusuario: todas las empresas del grupo
        if ($this->esSuperusuario()) {
            return Empresa::where('grupo_empresa_id', $grupoId)->get();
        }
        
        // Propietario o Administrador General del grupo: todas las empresas
        if ($this->puedeGestionarGrupo($grupoId)) {
            return Empresa::where('grupo_empresa_id', $grupoId)->get();
        }
        
        // Usuarios operativos: solo empresas asignadas del grupo
        $empresasConAcceso = $this->getEmpresasConAcceso();
        
        return $empresasConAcceso->filter(function ($empresa) use ($grupoId) {
            return $empresa->grupo_empresa_id === $grupoId;
        });
    }

    /**
     * Obtener ruta del dashboard según el rol
     */
    public function getRutaDashboard(): string
    {
        // Superusuario
        if ($this->esSuperusuario()) {
            return route('admin.dashboard');
        }
        
        // Administrador general o propietario
        if ($this->esAdministradorGeneral() || $this->esPropietario()) {
            if ($this->grupoEmpresa) {
                return route('grupo.dashboard', ['grupo' => $this->grupoEmpresa->slug]);
            }
        }
        
        // Usuario operativo
        // $empresa = $this->getEmpresaPrincipal();
        // if ($empresa) {
        //     return route('empresa.dashboard', [
        //         'grupo' => $empresa->grupoEmpresa->slug,
        //         'empresa' => $empresa->slug
        //     ]);
        // }
        
        // Fallback
        return route('dashboard');
    }

    /**
     * Cambiar a una empresa específica (contexto)
     */
    public function cambiarContextoEmpresa(int $empresaId): bool
    {
        if (!$this->tieneAccesoAEmpresa($empresaId)) {
            return false;
        }
        
        $this->empresa_id = $empresaId;
        return $this->save();
    }

    /**
     * Cambiar contexto completo (empresa y local)
     * 
     * Actualiza tanto la empresa como el local del contexto del usuario.
     * Guarda el contexto en sesión para uso durante la navegación.
     * 
     * @param int $empresaId ID de la empresa a seleccionar
     * @param int|null $localId ID del local (opcional)
     * @return bool True si el cambio fue exitoso
     */
    public function cambiarContexto(int $empresaId, ?int $localId = null): bool
    {
        // Verificar acceso a la empresa
        if (!$this->tieneAccesoAEmpresa($empresaId)) {
            return false;
        }

        // Si se proporciona local, verificar que pertenezca a la empresa
        if ($localId) {
            $local = Local::find($localId);
            
            if (!$local) {
                return false;
            }

            // Verificar que el local pertenezca a alguna de las empresas del usuario
            $empresaLocal = $local->empresas()->where('empresa_id', $empresaId)->first();
            
            if (!$empresaLocal) {
                return false;
            }
        }

        // Actualizar contexto en sesión
        session([
            'contexto_empresa_id' => $empresaId,
            'contexto_local_id' => $localId,
        ]);

        // Actualizar empresa principal si el usuario lo desea
        // (esto es opcional, depende de la lógica de negocio)
        $this->empresa_id = $empresaId;
        $this->local_id = $localId;
        
        return $this->save();
    }

    /**
     * Obtener el contexto actual del usuario desde la sesión
     * 
     * @return array ['empresa_id' => int|null, 'local_id' => int|null]
     */
    public function getContextoActual(): array
    {
        return [
            'empresa_id' => session('contexto_empresa_id', $this->empresa_id),
            'local_id' => session('contexto_local_id', $this->local_id),
        ];
    }

    /**
     * Obtener la empresa del contexto actual
     * 
     * @return Empresa|null
     */
    public function getEmpresaContexto(): ?Empresa
    {
        $contexto = $this->getContextoActual();
        
        if (!$contexto['empresa_id']) {
            return null;
        }

        return Empresa::find($contexto['empresa_id']);
    }

    /**
     * Obtener el local del contexto actual
     * 
     * @return Local|null
     */
    public function getLocalContexto(): ?Local
    {
        $contexto = $this->getContextoActual();
        
        if (!$contexto['local_id']) {
            return null;
        }

        return Local::find($contexto['local_id']);
    }

    /**
     * Registrar último acceso
     */
    // public function registrarAcceso(string $ip = null): void
    // {
    //     $this->ultimo_acceso = now();
    //     $this->ultimo_ip = $ip ?? request()->ip();
    //     $this->save();
    // }

    /**
     * Obtener configuración visual del usuario
     */
    public function getConfiguracionVisual(): array
    {
        $default = ['tema' => 'light'];
        
        // Configuración de empresa
        if ($this->empresa) {
            $default = $this->empresa->getConfiguracionVisual();
        }
        
        // Preferencias personales
        $preferencias = $this->preferencias ?? [];
        
        return array_merge($default, $preferencias);
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Avatar con fallback
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Generar avatar con iniciales
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3B82F6&color=fff';
    }

    /**
     * Nombre del rol principal
     */
    public function getRolPrincipalAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Contexto completo del usuario
     */
    public function getContextoCompletoAttribute(): array
    {
        return [
            'grupo' => $this->grupoEmpresa,
            'empresa' => $this->empresa,
            'sede' => $this->sede,
            'local' => $this->local,
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

        Log::info('Verificando superadmin para email: ' . $this->email);
        Log::info('Verificando superadmin para allowedEmails: ' . implode(', ', $allowedEmails));
        Log::info('Verificando superadmin para is_super_admin: ' . $this->is_super_admin);
        
        if (!in_array($this->email, $allowedEmails)) {
            Log::info('Verificando que no ingreso: ' . $this->email);
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
        if ($this->is_super_admin != true) {
            Log::info('Verificando que no is_super_admin: ');

            return false;
        }

        // 3. Verificación adicional: email verificado (si está habilitado)
        if (config('superadmin.require_email_verification', true) && !$this->hasVerifiedEmail()) {
            Log::info('Verificando que no require_email_verification: ');

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