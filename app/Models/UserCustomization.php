<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_mode',
        'theme_color',
        'custom_color',
        'font_family',
        'font_size',
        'layout_type',
        'layout_container',
        'navbar_type',
        'navbar_blur',
        'sidebar_type',
        'sidebar_collapsed',
        'rtl_mode',
        'custom_css',
    ];

    protected $casts = [
        'navbar_blur' => 'boolean',
        'sidebar_collapsed' => 'boolean',
        'rtl_mode' => 'boolean',
        'custom_css' => 'array',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener las configuraciones por defecto
     */
    public static function getDefaults(): array
    {
        return [
            'theme_mode' => 'system',
            'theme_color' => 'default',
            'custom_color' => null,
            'font_family' => 'inter',
            'font_size' => 'medium',
            'layout_type' => 'vertical',
            'layout_container' => 'fluid',
            'navbar_type' => 'fixed',
            'navbar_blur' => true,
            'sidebar_type' => 'fixed',
            'sidebar_collapsed' => false,
            'rtl_mode' => false,
            'custom_css' => null,
        ];
    }

    /**
     * Obtener los colores de tema disponibles
     */
    public static function getThemeColors(): array
    {
        return [
            'default' => '#696cff',
            'cyan' => '#00bcd4',
            'purple' => '#9c27b0',
            'orange' => '#ff9800',
            'red' => '#f44336',
            'green' => '#4caf50',
            'dark' => '#424242',
        ];
    }

    /**
     * Obtener las fuentes disponibles
     */
    public static function getFontFamilies(): array
    {
        return [
            'inter' => 'Inter',
            'roboto' => 'Roboto',
            'poppins' => 'Poppins',
            'open_sans' => 'Open Sans',
            'lato' => 'Lato',
        ];
    }
}
