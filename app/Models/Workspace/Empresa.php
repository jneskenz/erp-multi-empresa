<?php

namespace App\Models\Workspace;

use App\Models\GrupoEmpresa;
use App\Models\User;
use App\Models\Workspace\Local;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'empresas';

    protected $fillable = [
        'grupo_empresa_id',
        'nombre',
        'slug',
        'ruc',
        'razon_social',
        'nombre_comercial',
        'email',
        'telefono',
        'sitio_web',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'pais',
        'configuracion_visual',
        'modulos_activos',
        'logo',
        'favicon',
        'activo',
    ];

    protected $casts = [
        'configuracion_visual' => 'array',
        'modulos_activos' => 'array',
        'activo' => 'boolean',
    ];

    protected $attributes = [
        'pais' => 'Perú',
        'activo' => true,
    ];

    // ==================== BOOT ====================
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($empresa) {
            if (empty($empresa->slug)) {
                $empresa->slug = Str::slug($empresa->nombre);
            }
            
            // Slug único dentro del grupo
            $originalSlug = $empresa->slug;
            $count = 1;
            while (static::where('grupo_empresa_id', $empresa->grupo_empresa_id)
                       ->where('slug', $empresa->slug)
                       ->exists()) {
                $empresa->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });
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
     * Relación muchos-a-muchos con Locales
     * Una empresa puede operar en múltiples locales
     * Múltiples empresas pueden operar en el mismo local
     */
    public function locales()
    {
        return $this->belongsToMany(
            Local::class,
            'empresa_local',
            'empresa_id',
            'local_id'
        )->withPivot(['fecha_inicio', 'fecha_fin', 'es_principal', 'activo'])
         ->withTimestamps();
    }

    /**
     * Relación indirecta con Sedes a través de Locales
     * Una empresa puede operar en múltiples sedes
     */
    public function sedes()
    {
        return $this->hasManyThrough(
            Sede::class,
            Local::class,
            'id',          // FK en tabla local
            'id',          // FK en tabla sede
            'id',          // PK en tabla empresa
            'sede_id'      // FK en tabla local que apunta a sede
        )->join('empresa_local', function($join) {
            $join->on('empresa_local.local_id', '=', 'locales.id')
                 ->where('empresa_local.empresa_id', '=', $this->id);
        })->distinct();
    }

    /**
     * Usuarios directamente asignados
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'empresa_id');
    }

    /**
     * Usuarios con relación múltiple
     */
    public function usuariosMultiples(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user')
            ->withPivot(['es_principal', 'activo', 'fecha_asignacion', 'fecha_revocacion'])
            ->withTimestamps();
    }

    /**
     * Usuarios activos
     */
    public function usuariosActivos(): BelongsToMany
    {
        return $this->usuariosMultiples()->wherePivot('activo', true);
    }

    // ==================== SCOPES ====================
    
    /**
     * Empresas activas
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Empresas de un grupo
     */
    public function scopeDeGrupo($query, int $grupoId)
    {
        return $query->where('grupo_empresa_id', $grupoId);
    }

    /**
     * Buscar por slug dentro de un grupo
     */
    public function scopeBySlugEnGrupo($query, int $grupoId, string $slug)
    {
        return $query->where('grupo_empresa_id', $grupoId)
                     ->where('slug', $slug);
    }

    // ==================== MÉTODOS DE NEGOCIO ====================
    
    /**
     * Verificar si está activa
     */
    public function estaActiva(): bool
    {
        return $this->activo && $this->grupoEmpresa->estaActivo();
    }

    /**
     * Verificar si un módulo está activo
     */
    public function tieneModulo(string $modulo): bool
    {
        // Primero verificar si el grupo tiene el módulo
        if (!$this->grupoEmpresa->tieneModulo($modulo)) {
            return false;
        }
        
        // Luego verificar si la empresa lo tiene activado
        if (!$this->modulos_activos) {
            return false;
        }
        
        return in_array($modulo, $this->modulos_activos);
    }

    /**
     * Obtener configuración visual con cascada
     */
    public function getConfiguracionVisual(): array
    {
        // Configuración del grupo
        $configGrupo = $this->grupoEmpresa->getConfiguracionVisual();
        
        // Sobrescribir con configuración de la empresa
        $configEmpresa = $this->configuracion_visual ?? [];
        
        return array_merge($configGrupo, $configEmpresa);
    }

    /**
     * Asignar usuario a la empresa
     */
    public function asignarUsuario(User $usuario, bool $esPrincipal = false): bool
    {
        // Verificar si ya existe la relación
        if ($this->usuariosMultiples()->where('user_id', $usuario->id)->exists()) {
            return false;
        }
        
        $this->usuariosMultiples()->attach($usuario->id, [
            'es_principal' => $esPrincipal,
            'activo' => true,
            'fecha_asignacion' => now(),
        ]);
        
        // Si es principal, actualizar el usuario
        if ($esPrincipal) {
            $usuario->empresa_id = $this->id;
            $usuario->save();
        }
        
        activity()
            ->performedOn($this)
            ->causedBy($usuario)
            ->withProperties(['empresa' => $this->nombre])
            ->log('Usuario asignado a empresa');
        
        return true;
    }

    /**
     * Revocar acceso de usuario
     */
    public function revocarUsuario(User $usuario): bool
    {
        $this->usuariosMultiples()->updateExistingPivot($usuario->id, [
            'activo' => false,
            'fecha_revocacion' => now(),
        ]);
        
        activity()
            ->performedOn($this)
            ->causedBy($usuario)
            ->withProperties(['empresa' => $this->nombre])
            ->log('Acceso a empresa revocado');
        
        return true;
    }

    /**
     * Obtener ruta del dashboard de la empresa
     */
    public function rutaDashboard(): string
    {
        return route('empresa.dashboard', [
            'grupo' => $this->grupoEmpresa->slug,
            'empresa' => $this->slug
        ]);
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Nombre completo (comercial o razón social)
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre_comercial ?? $this->razon_social;
    }

    /**
     * URL de la empresa
     */
    public function getUrlAttribute(): string
    {
        return url($this->grupoEmpresa->slug . '/erp/' . $this->slug);
    }

    /**
     * Logo con fallback
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        
        return null;
    }

    /**
     * Opciones para activity log | Spatie
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'ruc',
                'razon_social', 
                'nombre_comercial',
                'direccion',
                'telefono',
                'correo',
                'estado',
                'codigo',
                'slug',
                'representante_legal',
                'grupo_empresa_id'
            ])  // Campos a registrar
            ->useLogName('empresa')
            ->setDescriptionForEvent(fn(string $eventName) => "Empresa ha sido {$eventName}") // Descripción personalizada
            ->logFillable() // Todos los campos fillable
            ->logOnlyDirty(); // Solo cambios en fillable
    }

}