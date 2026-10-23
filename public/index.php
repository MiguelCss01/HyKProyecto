<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Si el retorno de Mercado Pago ingresa por el puente HTTPS de XAMPP (ej. localhost:4433 o localhost:443)
// bajo /HyKProyecto/public o con PHP < 8.4, redirigimos a http://localhost:8000 conservando los parámetros GET.
if (
    isset($_SERVER['REQUEST_URI']) &&
    (PHP_VERSION_ID < 80400 || str_starts_with($_SERVER['REQUEST_URI'], '/HyKProyecto/public'))
) {
    $targetUri = preg_replace('#^/HyKProyecto/public#i', '', $_SERVER['REQUEST_URI']);
    $targetUri = '/'.ltrim((string) $targetUri, '/');
    header('Location: http://localhost:8000'.$targetUri, true, 302);
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
