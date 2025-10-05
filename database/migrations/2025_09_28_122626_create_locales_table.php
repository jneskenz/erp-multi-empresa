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
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_empresa_id')->constrained('grupo_empresas')->cascadeOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();

            $table->string('nombre', 100);
            $table->string('slug', 100);
            $table->string('codigo', 20)->unique();
            $table->string('descripcion', 255)->nullable();


            $table->enum('estado', [0, 1, 2, 4, 5])->default(1); 

            // Tipo de local
            $table->enum('tipo', ['tienda', 'almacen', 'oficina', 'fabrica', 'punto_venta', 'otro'])->default('tienda');
            
            // Ubicación específica
            $table->text('direccion')->nullable();
            $table->string('referencia', 255)->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            
            // Contacto
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            
            // Responsable
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Horarios
            $table->json('horarios')->nullable(); // {lunes: {inicio: '08:00', fin: '18:00'}, ...}
            
            // Capacidad y recursos
            $table->integer('capacidad_personas')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            
            // Estado
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->unique(['grupo_empresa_id', 'slug']);
            $table->index(['grupo_empresa_id', 'sede_id', 'estado']);
            $table->index('codigo');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
