<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class LeadCliente extends Model
{
    use HasFactory, HasRoles;

    protected $table = 'lead_clientes';

    protected $fillable = [
        'empresa',
        'ruc',
        'rubro_empresa',
        'nro_empleados',
        'pais',
        'descripcion',
        'cliente',
        'nro_documento',
        'correo',
        'telefono',
        'cargo',
        'plan_interes',
        'estado'
    ];

}
