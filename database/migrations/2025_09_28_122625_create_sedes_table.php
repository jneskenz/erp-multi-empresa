<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_empresa_id')->constrained('grupo_empresas')->cascadeOnDelete();
            
            $table->string('nombre', 100);
            $table->string('slug', 100);
            $table->string('codigo', 20)->nullable();
            $table->string('descripcion', 255)->nullable();
            
            // Ubicación
            $table->string('ciudad', 100);
            $table->string('departamento', 100);
            $table->string('pais', 50)->default('Perú');
            
            // Geolocalización
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            
            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('direccion')->nullable();
            
            // Estado
            $table->boolean('activo')->default(true);
            $table->boolean('es_principal')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->unique(['grupo_empresa_id', 'slug']);
            $table->index(['grupo_empresa_id', 'ciudad']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
