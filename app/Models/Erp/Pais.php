<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Pais extends Model
{
    use HasFactory, HasRoles, LogsActivity;
    
    protected $table = 'paises';

    protected $fillable = [
        'descripcion',
        'codigo',
        'moneda',
        'codigo_moneda',
        'simbolo_moneda',
        'estado',
    ];

    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'pais_id');
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'descripcion',
                'codigo',
                'moneda',
                'codigo_moneda',
                'simbolo_moneda',
                'estado'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
}
