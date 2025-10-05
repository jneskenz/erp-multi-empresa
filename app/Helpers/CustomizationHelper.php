<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('getUserCustomization')) {
    /**
     * Obtener las configuraciones de personalización del usuario autenticado
     */
    function getUserCustomization()
    {
        
        if (Auth::check()) {
            return Auth::user()->getCustomization();
        }
        
        // Valores por defecto si no hay usuario autenticado
        return (object) \App\Models\UserCustomization::getDefaults();
    }
}

if (!function_exists('getThemeClass')) {
    /**
     * Obtener la clase CSS para el tema
     */
    function getThemeClass($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        
        if ($customization->theme_mode === 'dark') {
            return 'dark-style';
        } elseif ($customization->theme_mode === 'light') {
            return 'light-style';
        } else {
            // Sistema - detectar preferencia (por defecto light)
            return 'light-style';
        }
    }
}

if (!function_exists('getThemeDataAttribute')) {
    /**
     * Obtener el atributo data-theme
     */
    function getThemeDataAttribute($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        
        if ($customization->theme_mode === 'dark') {
            return 'theme-dark';
        } else {
            return 'theme-default';
        }
    }
}

if (!function_exists('getLayoutClasses')) {
    /**
     * Obtener las clases CSS para el layout
     */
    function getLayoutClasses($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        $classes = [];
        
        // Layout type
        if ($customization->layout_type === 'vertical') {
            $classes[] = 'layout-navbar-fixed';
            $classes[] = 'layout-menu-fixed';
            
            if ($customization->sidebar_collapsed) {
                $classes[] = 'layout-menu-collapsed';
            }
        } else {
            $classes[] = 'layout-navbar-full';
            $classes[] = 'layout-horizontal';
            $classes[] = 'layout-without-menu';
        }
        
        // Navbar type
        if ($customization->navbar_type === 'static') {
            $classes = array_filter($classes, function($class) {
                return $class !== 'layout-navbar-fixed';
            });
        } elseif ($customization->navbar_type === 'hidden') {
            $classes[] = 'layout-navbar-hidden';
        }
        
        // Container type
        if ($customization->layout_container === 'boxed') {
            $classes[] = 'layout-container-boxed';
        } elseif ($customization->layout_container === 'detached') {
            $classes[] = 'layout-container-detached';
        }
        
        // Navbar blur
        if ($customization->navbar_blur) {
            $classes[] = 'layout-navbar-blur';
        }
        
        $classes[] = 'layout-compact';
        
        return implode(' ', $classes);
    }
}

if (!function_exists('getTemplateDataAttribute')) {
    /**
     * Obtener el atributo data-template
     */
    function getTemplateDataAttribute($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        
        if ($customization->layout_type === 'horizontal') {
            return 'horizontal-menu-template-starter';
        } else {
            return 'vertical-menu-template-starter';
        }
    }
}

if (!function_exists('getCustomStyles')) {
    /**
     * Obtener los estilos CSS personalizados
     */
    function getCustomStyles($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        $styles = [];
        
        // Color del tema
        if ($customization->theme_color === 'custom' && $customization->custom_color) {
            $color = $customization->custom_color;
            $rgb = hexToRgb($color);
            $styles[] = "--bs-primary: {$color};";
            $styles[] = "--bs-primary-rgb: {$rgb};";
        } elseif ($customization->theme_color !== 'default') {
            $themeColors = \App\Models\UserCustomization::getThemeColors();
            if (isset($themeColors[$customization->theme_color])) {
                $color = $themeColors[$customization->theme_color];
                $rgb = hexToRgb($color);
                $styles[] = "--bs-primary: {$color};";
                $styles[] = "--bs-primary-rgb: {$rgb};";
            }
        }
        
        // Tamaño de fuente
        $fontSizes = [
            'small' => '0.85rem',
            'medium' => '0.9375rem',
            'large' => '1rem'
        ];
        
        if (isset($fontSizes[$customization->font_size])) {
            $styles[] = "font-size: {$fontSizes[$customization->font_size]};";
        }
        
        return $styles ? implode(' ', $styles) : '';
    }
}

if (!function_exists('getFontFamily')) {
    /**
     * Obtener la familia de fuente
     */
    function getFontFamily($customization = null)
    {
        $customization = $customization ?: getUserCustomization();
        
        $fontFamilies = [
            'inter' => 'Inter, sans-serif',
            'roboto' => 'Roboto, sans-serif',
            'poppins' => 'Poppins, sans-serif',
            'open_sans' => 'Open Sans, sans-serif',
            'lato' => 'Lato, sans-serif'
        ];
        
        return $fontFamilies[$customization->font_family] ?? 'Inter, sans-serif';
    }
}

if (!function_exists('hexToRgb')) {
    /**
     * Convertir color hexadecimal a RGB
     */
    function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        
        if (strlen($hex) === 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . 
                   str_repeat(substr($hex, 1, 1), 2) . 
                   str_repeat(substr($hex, 2, 1), 2);
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "{$r}, {$g}, {$b}";
    }
}