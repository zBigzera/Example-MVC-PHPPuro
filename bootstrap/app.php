<?php
require __DIR__ . "/../vendor/autoload.php";

use \App\Core\View;
use \WilliamCosta\DatabaseManager\Database;
use \App\Core\Http\Middlewares\QueueMiddleware  as MiddlewareQueue;
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
    'maintenance' => \App\Core\Http\Middlewares\Maintenance::class,
    'require-admin-logout' => \App\Core\Http\Middlewares\RequireAdminLogout::class,
    'require-admin-login' => \App\Core\Http\Middlewares\RequireAdminLogin::class,
    'api' => \App\Core\Http\Middlewares\Api::class,
    'user-basic-auth' => \App\Core\Http\Middlewares\UserBasicAuth::class,
    'jwt-auth' => \App\Core\Http\Middlewares\JWTAuth::class,
    'cache' => \App\Core\Http\Middlewares\Cache::class,
]);

//Define o mapeamento de middlewares padrões (em todas as rotas)
MiddlewareQueue::setDefault([
    'maintenance'
]);