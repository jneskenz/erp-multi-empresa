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
            $table->string('numerodocumento', 20)->unique();
            $table->string('razon_social', 100);
            $table->string('nombre_comercial', 100)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('avatar', 200)->nullable();
            $table->string('representante_legal', 100)->nullable();
            $table->boolean('estado')->default(1)->comment('1=activo,0=inactivo,5=eliminado');
            
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->foreign('pais_id')->references('id')->on('paises')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
        });
        Schema::dropIfExists('empresas');
    }
};
