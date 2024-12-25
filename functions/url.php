<?php
function url($path = '') {
    if (!defined('BASE_URL')) {
        define('BASE_URL', '/shopping-website/');
    }
    return BASE_URL . ltrim($path, '/');
}