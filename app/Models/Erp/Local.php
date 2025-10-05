<?php

namespace App\Models\Erp;

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
        'descripcion',
        'codigo',
        'direccion',
        'correo',
        'telefono',
        'whatsapp',
        'estado',
        'sede_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    * Relacion: Local pertenece a una Sede
    */
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

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
