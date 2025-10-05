<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AuditoriaMultiempresa
{
   /**
    * Boot del trait
    */
   protected static function bootAuditoriaMultiempresa()
   {
      // Registrar evento al crear
      static::creating(function ($model) {
         $user = Auth::user();

         if ($user) {
            activity()
               ->performedOn($model)
               ->causedBy($user)
               ->withProperties([
                  'attributes' => $model->getAttributes(),
                  'grupo_empresa_id' => $user->grupo_empresa_id,
                  'empresa_id' => $user->empresa_id,
                  'ip' => request()->ip(),
               ])
               ->log('Creó ' . class_basename($model));
         }
      });

      // Registrar evento al actualizar
      static::updating(function ($model) {
         $user = Auth::user();

         if ($user) {
            activity()
               ->performedOn($model)
               ->causedBy($user)
               ->withProperties([
                  'old' => $model->getOriginal(),
                  'attributes' => $model->getAttributes(),
                  'grupo_empresa_id' => $user->grupo_empresa_id,
                  'empresa_id' => $user->empresa_id,
                  'ip' => request()->ip(),
               ])
               ->log('Actualizó ' . class_basename($model));
         }
      });

      // Registrar evento al eliminar
      static::deleting(function ($model) {
         $user = Auth::user();

         if ($user) {
            activity()
               ->performedOn($model)
               ->causedBy($user)
               ->withProperties([
                  'attributes' => $model->getAttributes(),
                  'grupo_empresa_id' => $user->grupo_empresa_id,
                  'empresa_id' => $user->empresa_id,
                  'ip' => request()->ip(),
               ])
               ->log('Eliminó ' . class_basename($model));
         }
      });

      // Registrar evento al restaurar (soft deletes)
      static::restoring(function ($model) {
         $user = Auth::user();

         if ($user) {
            activity()
               ->performedOn($model)
               ->causedBy($user)
               ->withProperties([
                  'attributes' => $model->getAttributes(),
                  'grupo_empresa_id' => $user->grupo_empresa_id,
                  'empresa_id' => $user->empresa_id,
                  'ip' => request()->ip(),
               ])
               ->log('Restauró ' . class_basename($model));
         }
      });
   }

   /**
    * Obtener actividad reciente del modelo
    */
   public function actividadReciente($limit = 10)
   {
      return activity()
         ->forSubject($this)
         ->latest()
         ->limit($limit)
         ->get();
   }

   /**
    * Obtener quién creó el modelo
    */
   public function creadoPor()
   {
      return activity()
         ->forSubject($this)
         ->where('description', 'like', 'Creó%')
         ->first()
         ?->causer;
   }

   /**
    * Obtener última modificación
    */
   public function ultimaModificacion()
   {
      return activity()
         ->forSubject($this)
         ->where('description', 'like', 'Actualizó%')
         ->latest()
         ->first();
   }
}
