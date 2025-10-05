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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_empresa_id')->constrained('grupo_empresas')->cascadeOnDelete();
            
            $table->string('nombre', 100);
            $table->string('slug', 100);
            $table->string('ruc', 11)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();

            // Contacto
            $table->string('email', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('sitio_web', 200)->nullable();
            $table->string('representante_legal', 100)->nullable();

            // Dirección
            $table->string('direccion', 255)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('pais', 50)->default('Perú');

            // Configuración visual heredable
            $table->json('configuracion_visual')->nullable();

            // Módulos asignados
            $table->json('modulos_activos')->nullable();
            
            // Logo y branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            
            // Estado
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->unique(['grupo_empresa_id', 'slug']);
            $table->index(['grupo_empresa_id', 'activo']);
            $table->index('ruc');           
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('empresas');
    }
};
