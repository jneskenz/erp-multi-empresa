<?php

namespace App\Models\Workspace;

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

    // Relación con el modelo Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
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
