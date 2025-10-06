<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remover empresa_id de locales porque ahora es relación N:M a través de empresa_local
     */
    public function up(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            // Primero eliminar la foreign key
            $table->dropForeign(['empresa_id']);
            // Luego eliminar la columna
            $table->dropColumn('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            // Restaurar la columna si se revierte la migración
            $table->foreignId('empresa_id')->nullable()->after('grupo_empresa_id')->constrained('empresas')->cascadeOnDelete();
        });
    }
};
