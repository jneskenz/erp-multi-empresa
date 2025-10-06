<?php

namespace App\Models\Workspace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Local extends Model
{
    use HasFactory, HasRoles, SoftDeletes, LogsActivity;

    protected $table = 'locales';

    protected $fillable = [
        'grupo_empresa_id',
        'sede_id',
        'nombre',
        'slug',
        'codigo',
        'descripcion',
        'tipo',
        'direccion',
        'referencia',
        'latitud',
        'longitud',
        'email',
        'telefono',
        'whatsapp',
        'responsable_id',
        'horarios',
        'capacidad_personas',
        'area_m2',
        'activo',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ==================== RELACIONES ====================
    
    /**
     * Relación con GrupoEmpresa
     * Un local pertenece a un grupo empresarial
     */
    public function grupoEmpresa()
    {
        return $this->belongsTo(\App\Models\GrupoEmpresa::class, 'grupo_empresa_id');
    }

    /**
     * Relación con Sede
     * Un local pertenece a una sede (ubicación física dentro del grupo)
     */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación muchos-a-muchos con Empresas
     * Un local puede tener múltiples empresas operando en él
     * Múltiples empresas pueden compartir el mismo local
     */
    public function empresas()
    {
        return $this->belongsToMany(
            Empresa::class,
            'empresa_local',
            'local_id',
            'empresa_id'
        )->withPivot(['fecha_inicio', 'fecha_fin', 'es_principal', 'activo'])
         ->withTimestamps();
    }

    // ==================== SCOPES ====================
    
    /**
     * Scope para filtrar locales activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar por sede
     */
    public function scopePorSede($query, $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly([
                'descripcion',
                'codigo',
                'direccion',
                'correo',
                'telefono',
                'whatsapp',
                'estado',
                'sede_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope para filtrar locales activos
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('descripcion', 'LIKE', "%$termino%")
                ->orWhere('codigo', 'LIKE', "%$termino%")
                ->orWhere('direccion', 'LIKE', "%$termino%")
                ->orWhere('correo', 'LIKE', "%$termino%")
                ->orWhere('telefono', 'LIKE', "%$termino%")
                ->orWhere('whatsapp', 'LIKE', "%$termino%")
                ->orWhereHas('sede', function ($sedeQuery) use ($termino) {
                    $sedeQuery->where('nombre', 'LIKE', "%$termino%")
                                ->orWhere('descripcion', 'LIKE', "%$termino%");
                });
        });
    }


}
