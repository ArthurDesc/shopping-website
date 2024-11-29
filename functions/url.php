<?php
function url($path = '') {
    $base_url = '/php-vanilla/shopping-website/'; // Chemin depuis la racine web
    return $base_url . ltrim($path, '/');
}