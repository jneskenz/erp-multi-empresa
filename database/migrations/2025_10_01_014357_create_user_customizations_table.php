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
        Schema::create('user_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Configuración de tema
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('system');
            $table->enum('theme_color', ['default', 'cyan', 'purple', 'orange', 'red', 'green', 'dark', 'custom'])->default('default');
            $table->string('custom_color')->nullable(); // Para tema personalizado
            
            // Configuración de fuente
            $table->enum('font_family', ['inter', 'roboto', 'poppins', 'open_sans', 'lato'])->default('inter');
            $table->enum('font_size', ['small', 'medium', 'large'])->default('medium');
            
            // Configuración de layout
            $table->enum('layout_type', ['vertical', 'horizontal'])->default('vertical');
            $table->enum('layout_container', ['fluid', 'boxed', 'detached'])->default('fluid');
            
            // Configuración de navbar
            $table->enum('navbar_type', ['fixed', 'static', 'hidden'])->default('fixed');
            $table->boolean('navbar_blur')->default(true);
            
            // Configuración de sidebar (solo para layout vertical)
            $table->enum('sidebar_type', ['fixed', 'static', 'hidden'])->default('fixed');
            $table->boolean('sidebar_collapsed')->default(false);
            
            // Configuración adicional
            $table->boolean('rtl_mode')->default(false);
            $table->json('custom_css')->nullable(); // Para estilos personalizados
            
            $table->timestamps();
            
            // Índices
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_customizations');
    }
};
