<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Utils\View;
use \WilliamCosta\DatabaseManager\Database;
use \App\Http\Middleware\Queue as MiddlewareQueue;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

Database::config($_ENV['DB_HOST'],$_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASS']);
$urlFromEnv = $_ENV['URL'] ?? null;

if ($urlFromEnv) {
    define('URL', rtrim($urlFromEnv, '/'));
} else {
    $host = $_SERVER['HTTP_HOST'];
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('URL', $scheme . '://' . $host);
}

View::init(['URL' => URL]);

//Define o mapeamento de middlewares
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'require-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'require-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'api' => \App\Http\Middleware\Api::class
]);

//Define o mapeamento de middlewares padrões (em todas as rotas)
MiddlewareQueue::setDefault([
    'maintenance'
]);