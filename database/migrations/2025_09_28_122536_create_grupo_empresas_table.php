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
        Schema::create('grupo_empresas', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid')->unique();
            $table->string('nombre', 100);
            $table->string('slug', 100)->unique();
            $table->string('ruc', 11)->unique()->nullable();
            $table->text('descripcion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logo', 200)->nullable();
            $table->string('favicon', 200)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('sitio_web', 200)->nullable();
            // pais_id
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->foreign('pais_id')->references('id')->on('paises')->onDelete('set null');
            
            // Configuración visual
            $table->json('configuracion_visual')->nullable();
            
            // Plan y límites
            $table->string('plan_actual')->default('basico'); // basico, profesional, empresarial
            $table->integer('max_empresas')->default(1);
            $table->integer('max_usuarios')->default(10);
            $table->json('modulos_habilitados')->nullable(); // ['erp', 'crm', 'rrhh', 'web']
            
            // Estado y auditoría
           $table->enum('estado', ['activo', 'suspendido', 'inactivo'])->default('activo');
            $table->timestamp('fecha_activacion')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['slug', 'estado']);
            $table->index('ruc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_empresas');
    }
};
