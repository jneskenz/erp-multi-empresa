<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivot para relación N:M entre Empresas y Locales
     * Permite que múltiples empresas operen en el mismo local
     */
    public function up(): void
    {
        Schema::create('empresa_local', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('local_id')->constrained('locales')->cascadeOnDelete();
            
            // Metadatos de la relación
            $table->date('fecha_inicio')->nullable()->comment('Fecha desde que la empresa opera en este local');
            $table->date('fecha_fin')->nullable()->comment('Fecha hasta que la empresa operó en este local');
            $table->boolean('es_principal')->default(false)->comment('Si es el local principal de esta empresa');
            $table->boolean('activo')->default(true)->comment('Si la empresa sigue operando en este local');
            
            $table->timestamps();
            
            // Restricciones
            $table->unique(['empresa_id', 'local_id'], 'unique_empresa_local');
            $table->index(['empresa_id', 'activo']);
            $table->index(['local_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_local');
    }
};
