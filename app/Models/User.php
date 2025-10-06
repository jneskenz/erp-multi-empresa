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
            ->withPivot(['es_principal', 'activo', 'fecha_asignacion', 'fecha_revocacion'])
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
        $empresa = $this->getEmpresaPrincipal();
        if ($empresa) {
            return route('empresa.dashboard', [
                'grupo' => $empresa->grupoEmpresa->slug,
                'empresa' => $empresa->slug
            ]);
        }
        
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
     * Registrar último acceso
     */
    public function registrarAcceso(string $ip = null): void
    {
        $this->ultimo_acceso = now();
        $this->ultimo_ip = $ip ?? request()->ip();
        $this->save();
    }

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
}