<?php
function url($path = '') {
    if (!defined('BASE_URL')) {
        // Détection de l'environnement de production
        $is_production = $_SERVER['HTTP_HOST'] === 'derroce.com' || strpos($_SERVER['HTTP_HOST'], 'derroce.com') !== false;
        
        if ($is_production) {
            // En production, on utilise /shopping-website/ comme base
            define('BASE_URL', '/shopping-website/');
        } else {
            // En local, on utilise le chemin relatif
            $script_name = $_SERVER['SCRIPT_NAME'];
            $base_dir = dirname(dirname($script_name));
            if ($base_dir == '\\' || $base_dir == '/') {
                define('BASE_URL', '/');
            } else {
                define('BASE_URL', $base_dir . '/');
            }
        }
    }
    return BASE_URL . ltrim($path, '/');
}