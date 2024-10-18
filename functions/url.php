<?php
function url($path = '') {
    $base_url = '/shopping-website/'; // Ajustez selon votre configuration
    return $base_url . ltrim($path, '/');
}