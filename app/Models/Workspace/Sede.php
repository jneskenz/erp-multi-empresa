<?php

namespace App\Models\Workspace;

use App\Models\GrupoEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Sede extends Model
{

    use HasFactory, HasRoles, LogsActivity;

    protected $table = 'sedes';

    protected $fillable = [
        'nombre',
        'slug',
        'codigo',
        'descripcion',
        'ciudad',
        'departamento',
        'pais',
        'latitud',
        'longitud',
        'telefono',
        'email',
        'direccion',
        'activo',
        'es_principal',
        'grupo_empresa_id',
    ];

    /**
     * Relación con GrupoEmpresa
     * Una sede pertenece a un grupo empresarial
     */
    public function grupoEmpresa()
    {
        return $this->belongsTo(GrupoEmpresa::class, 'grupo_empresa_id');
    }

    /**
     * Relación con Locales
     * Una sede puede tener muchos locales
     */
    public function locales()
    {
        return $this->hasMany(Local::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre',
                'codigo',
                'descripcion',
                'estado',
                'empresa_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

}
