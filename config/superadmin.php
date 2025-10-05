<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lista de Superadministradores
    |--------------------------------------------------------------------------
    |
    | Emails hardcodeados de usuarios que pueden ser superadministradores.
    | Esta es la ÚNICA manera de ser superadmin - estar en esta lista.
    | Esto previene que usuarios maliciosos modifiquen la BD para ser superadmin.
    |
    */
    'allowed_emails' => [
        'superadmin@erpmultisoft.com',
        'admin@erpmultisoft.com', // Solo para desarrollo
        // Agregar más emails de superadmins aquí
    ],

    /*
    |--------------------------------------------------------------------------
    | Validación adicional
    |--------------------------------------------------------------------------
    |
    | Configuraciones adicionales de seguridad para superadministradores
    |
    */
    'require_email_verification' => true,
    'require_2fa' => false, // Para futuras implementaciones
    
    /*
    |--------------------------------------------------------------------------
    | Logs de seguridad
    |--------------------------------------------------------------------------
    |
    | Configurar si se deben registrar accesos de superadministradores
    |
    */
    'log_superadmin_access' => true,
];