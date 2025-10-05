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
        // Extender tabla users
        Schema::table('users', function (Blueprint $table) {
            // Contexto multiempresa
            $table->foreignId('grupo_empresa_id')->nullable()->after('id')->constrained('grupo_empresas')->nullOnDelete();
            $table->foreignId('empresa_id')->nullable()->after('grupo_empresa_id')->constrained('empresas')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->after('empresa_id')->constrained('sedes')->nullOnDelete();
            $table->foreignId('local_id')->nullable()->after('sede_id')->constrained('locales')->nullOnDelete();
            
            // Información adicional
            $table->string('documento_tipo', 20)->nullable()->after('email'); // DNI, RUC, CE, PASAPORTE
            $table->string('documento_numero', 20)->nullable()->after('documento_tipo');
            $table->string('telefono', 20)->nullable()->after('documento_numero');
            $table->date('fecha_nacimiento')->nullable()->after('telefono');
            
            // Configuración personal
            $table->json('preferencias')->nullable(); // tema, idioma, etc.
            $table->string('avatar')->nullable();
            
            // Estado y auditoría
            $table->boolean('activo')->default(true)->after('remember_token');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            
            // Índices
            $table->index(['grupo_empresa_id', 'activo']);
            $table->index(['empresa_id', 'activo']);
            $table->index('documento_numero');
        });

        // Tabla pivote: usuarios pueden pertenecer a múltiples empresas
        Schema::create('empresa_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Configuración por relación
            $table->boolean('es_principal')->default(false); // Empresa por defecto del usuario
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->timestamp('fecha_revocacion')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->unique(['empresa_id', 'user_id']);
            $table->index(['user_id', 'es_principal']);
        });

        // Tabla: propietarios de grupos empresariales
        Schema::create('grupo_empresa_propietario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_empresa_id')->constrained('grupo_empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Nivel de propiedad
            $table->enum('tipo', ['principal', 'asociado'])->default('principal');
            $table->decimal('porcentaje_participacion', 5, 2)->default(0.00);
            
            // Permisos específicos
            $table->boolean('puede_modificar_plan')->default(false);
            $table->boolean('puede_crear_empresas')->default(true);
            $table->boolean('puede_asignar_usuarios')->default(true);
            
            $table->timestamp('fecha_desde')->useCurrent();
            $table->timestamp('fecha_hasta')->nullable();
            $table->boolean('activo')->default(true);
            
            $table->timestamps();
            
            // Índices
            $table->unique(['grupo_empresa_id', 'user_id']);
            $table->index(['user_id', 'activo']);
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_empresa_propietario');
        Schema::dropIfExists('empresa_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropForeign(['sede_id']);
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['grupo_empresa_id']);
            
            $table->dropColumn([
                'grupo_empresa_id', 'empresa_id', 'sede_id', 'local_id',
                'documento_tipo', 'documento_numero', 'telefono', 'fecha_nacimiento',
                'preferencias', 'avatar', 'activo', 'ultimo_acceso', 'ultimo_ip'
            ]);
        });
    }
};
