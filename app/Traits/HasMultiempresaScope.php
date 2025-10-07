<?php

namespace App\Traits;

use App\Models\Scopes\FiltroMultiempresaScope;

/**
 * Trait para aplicar el filtro multiempresa automáticamente
 * 
 * Uso:
 * 
 * use HasMultiempresaScope;
 * 
 * Este trait aplica el FiltroMultiempresaScope automáticamente
 * al modelo para filtrar registros según el contexto del usuario.
 * 
 * Para desactivar temporalmente el filtro:
 * Modelo::sinFiltroMultiempresa()->get();
 * 
 * Para filtrar por empresa específica:
 * Modelo::deEmpresa($empresaId)->get();
 * 
 * Para filtrar por todas las empresas del usuario:
 * Modelo::deTodasMisEmpresas()->get();
 */
trait HasMultiempresaScope
{
    /**
     * Boot the trait
     */
    protected static function bootHasMultiempresaScope(): void
    {
        static::addGlobalScope(new FiltroMultiempresaScope);
    }
}
