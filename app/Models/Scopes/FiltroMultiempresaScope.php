<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

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

      // Para usuarios operativos, filtrar por empresa si corresponde
      if ($this->tieneColumna($model, 'empresa_id') && $user->empresa_id) {
         // Obtener empresas con acceso
         $empresasIds = $user->empresasActivas()->pluck('empresas.id')->toArray();

         if (!empty($empresasIds)) {
            $builder->whereIn($model->getTable() . '.empresa_id', $empresasIds);
         }
      }
   }

   /**
    * Extender el builder para permitir desactivar el scope
    */
   public function extend(Builder $builder): void
   {
      $builder->macro('sinFiltroMultiempresa', function (Builder $builder) {
         return $builder->withoutGlobalScope($this);
      });

      $builder->macro('deGrupo', function (Builder $builder, int $grupoId) {
         return $builder->withoutGlobalScope($this)
            ->where($builder->getModel()->getTable() . '.grupo_empresa_id', $grupoId);
      });

      $builder->macro('deEmpresa', function (Builder $builder, int $empresaId) {
         return $builder->withoutGlobalScope($this)
            ->where($builder->getModel()->getTable() . '.empresa_id', $empresaId);
      });
   }

   /**
    * Verificar si el modelo tiene una columna específica
    */
   private function tieneColumna(Model $model, string $columna): bool
   {
      $tabla = $model->getTable();
      $schema = $model->getConnection()->getSchemaBuilder();

      return $schema->hasColumn($tabla, $columna);
   }
}
