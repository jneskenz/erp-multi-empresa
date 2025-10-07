<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Scope Global para filtrado automático por contexto multiempresa
 * 
 * Este scope se aplica automáticamente a todos los modelos del ERP
 * para filtrar registros según el contexto del usuario:
 * 
 * - Superusuario: Ve todos los registros sin filtro
 * - Administrador General/Propietario: Ve todo su grupo empresarial
 * - Usuarios operativos: Solo ven registros de sus empresas asignadas
 * 
 * El filtrado se basa en:
 * 1. Contexto de sesión (empresa_id y local_id actuales)
 * 2. Empresas asignadas al usuario (tabla empresa_user)
 * 3. Grupo empresarial del usuario
 */
class FiltroMultiempresaScope implements Scope
{
    /**
     * Aplicar scope al query builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Si no hay usuario autenticado, no aplicar filtro
        if (!$user) {
            return;
        }

        // Si es superusuario, no aplicar filtro (ve todo)
        if ($user->esSuperusuario()) {
            return;
        }

        // Filtrar por grupo empresarial si el modelo tiene esa columna
        if ($this->tieneColumna($model, 'grupo_empresa_id') && $user->grupo_empresa_id) {
            $builder->where($model->getTable() . '.grupo_empresa_id', $user->grupo_empresa_id);
        }

        // Si es administrador general o propietario, puede ver todo del grupo
        if ($user->esAdministradorGeneral() || $user->esPropietario()) {
            return;
        }

        // Para usuarios operativos, filtrar por empresa
        $this->aplicarFiltroEmpresa($builder, $model, $user);

        // Filtrar por local si el modelo lo tiene y hay contexto
        $this->aplicarFiltroLocal($builder, $model, $user);
    }

    /**
     * Aplicar filtro por empresa para usuarios operativos
     */
    private function aplicarFiltroEmpresa(Builder $builder, Model $model, $user): void
    {
        if (!$this->tieneColumna($model, 'empresa_id')) {
            return;
        }

        // Obtener contexto actual de la sesión
        $contexto = $user->getContextoActual();
        $empresaContexto = $contexto['empresa_id'];

        // Si hay empresa en contexto, filtrar por ella
        if ($empresaContexto) {
            $builder->where($model->getTable() . '.empresa_id', $empresaContexto);
            return;
        }

        // Si no hay contexto, usar empresas asignadas
        $empresasIds = $user->empresasActivas()->pluck('empresas.id')->toArray();
        
        // Agregar empresa principal si existe
        if ($user->empresa_id && !in_array($user->empresa_id, $empresasIds)) {
            $empresasIds[] = $user->empresa_id;
        }

        if (!empty($empresasIds)) {
            $builder->whereIn($model->getTable() . '.empresa_id', $empresasIds);
        } else {
            // Si no tiene empresas asignadas, no mostrar nada
            $builder->whereRaw('1 = 0');
        }
    }

    /**
     * Aplicar filtro por local si el modelo lo tiene
     */
    private function aplicarFiltroLocal(Builder $builder, Model $model, $user): void
    {
        if (!$this->tieneColumna($model, 'local_id')) {
            return;
        }

        // Obtener contexto actual de la sesión
        $contexto = $user->getContextoActual();
        $localContexto = $contexto['local_id'];

        // Solo filtrar si hay local en contexto
        if ($localContexto) {
            $builder->where($model->getTable() . '.local_id', $localContexto);
        }
    }

    /**
     * Extender el builder con macros útiles
     * 
     * Permite desactivar el scope temporalmente o aplicar filtros específicos
     */
    public function extend(Builder $builder): void
    {
        // Desactivar completamente el filtro multiempresa
        $builder->macro('sinFiltroMultiempresa', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        // Filtrar por un grupo específico
        $builder->macro('deGrupo', function (Builder $builder, int $grupoId) {
            return $builder->withoutGlobalScope($this)
                ->where($builder->getModel()->getTable() . '.grupo_empresa_id', $grupoId);
        });

        // Filtrar por una empresa específica
        $builder->macro('deEmpresa', function (Builder $builder, int $empresaId) {
            return $builder->withoutGlobalScope($this)
                ->where($builder->getModel()->getTable() . '.empresa_id', $empresaId);
        });

        // Filtrar por múltiples empresas
        $builder->macro('deEmpresas', function (Builder $builder, array $empresasIds) {
            return $builder->withoutGlobalScope($this)
                ->whereIn($builder->getModel()->getTable() . '.empresa_id', $empresasIds);
        });

        // Filtrar por un local específico
        $builder->macro('deLocal', function (Builder $builder, int $localId) {
            return $builder->withoutGlobalScope($this)
                ->where($builder->getModel()->getTable() . '.local_id', $localId);
        });

        // Filtrar por contexto específico (empresa + local)
        $builder->macro('conContexto', function (Builder $builder, int $empresaId, ?int $localId = null) {
            $query = $builder->withoutGlobalScope($this)
                ->where($builder->getModel()->getTable() . '.empresa_id', $empresaId);
            
            if ($localId && in_array('local_id', $builder->getModel()->getFillable())) {
                $query->where($builder->getModel()->getTable() . '.local_id', $localId);
            }
            
            return $query;
        });

        // Obtener registros de todas las empresas del usuario
        $builder->macro('deTodasMisEmpresas', function (Builder $builder) {
            $user = Auth::user();
            
            if (!$user) {
                return $builder->whereRaw('1 = 0');
            }

            if ($user->esSuperusuario()) {
                return $builder->withoutGlobalScope($this);
            }

            $empresasIds = $user->empresasActivas()->pluck('empresas.id')->toArray();
            
            if ($user->empresa_id && !in_array($user->empresa_id, $empresasIds)) {
                $empresasIds[] = $user->empresa_id;
            }

            return $builder->withoutGlobalScope($this)
                ->whereIn($builder->getModel()->getTable() . '.empresa_id', $empresasIds);
        });
    }

    /**
     * Verificar si el modelo tiene una columna específica
     * 
     * Usa caché estático para evitar múltiples consultas al schema
     */
    private function tieneColumna(Model $model, string $columna): bool
    {
        static $cache = [];
        
        $tabla = $model->getTable();
        $cacheKey = $tabla . '.' . $columna;

        if (!isset($cache[$cacheKey])) {
            // Verificar en fillable primero (más rápido)
            if (in_array($columna, $model->getFillable())) {
                $cache[$cacheKey] = true;
            } else {
                // Verificar en schema como fallback
                $schema = $model->getConnection()->getSchemaBuilder();
                $cache[$cacheKey] = $schema->hasColumn($tabla, $columna);
            }
        }

        return $cache[$cacheKey];
    }
}

