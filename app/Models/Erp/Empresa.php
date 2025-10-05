<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    
    use HasFactory, HasRoles, LogsActivity;

    protected $table = 'empresas';

    protected $fillable = [
        'numerodocumento',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'telefono',
        'correo',
        'avatar',
        'estado',
        'pais_id',
        'representante_legal'
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function sedes()
    {
        return $this->hasMany(Sede::class, 'empresa_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'numerodocumento',
                'razon_social', 
                'nombre_comercial',
                'direccion',
                'telefono',
                'correo',
                'estado',
                'pais_id',
                'representante_legal'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
