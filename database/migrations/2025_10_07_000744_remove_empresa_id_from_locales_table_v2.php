<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina el campo empresa_id de la tabla locales ya que la relación
     * entre empresas y locales se maneja a través de la tabla pivot empresa_local.
     */
    public function up(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            // Primero eliminar la foreign key constraint si existe
            $table->dropForeign(['empresa_id']);
            
            // Luego eliminar la columna
            $table->dropColumn('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura el campo empresa_id en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            // Recrear la columna
            $table->foreignId('empresa_id')->nullable()->after('grupo_empresa_id');
            
            // Recrear la foreign key
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
        });
    }
};
