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
        Schema::create('configuraciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('tipo', 50)->default('texto'); // texto, json, boolean, numero
            $table->string('grupo', 50)->default('general'); // general, visual, email, etc.
            $table->text('descripcion')->nullable();
            $table->boolean('es_publico')->default(false);
            $table->timestamps();

            $table->index(['grupo', 'es_publico']);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_sistema');
    }
};
