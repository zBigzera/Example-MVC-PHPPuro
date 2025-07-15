<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Core\Container;
use App\Core\Database\DatabaseServiceProvider;
use App\Core\View;
use App\Core\Http\Middlewares\QueueMiddleware as MiddlewareQueue;

// Carrega as variáveis de ambiente
$envFile = __DIR__ . "/../.env";

if (file_exists($envFile)) {

    $dotenv = \Dotenv\Dotenv::createImmutable(dirname($envFile));
    $dotenv->load();
}


// Configuração do Database (agora via ServiceProvider)
DatabaseServiceProvider::register();

// Configuração de Timezone
date_default_timezone_set(getenv("TIMEZONE") ?: 'America/Sao_Paulo');

// Definição da URL
$urlFromEnv = getenv('URL') ?: null;

if ($urlFromEnv) {
    define('URL', rtrim($urlFromEnv, '/'));
} else {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('URL', $scheme . '://' . $host);
}

// Inicializa a View com a URL
View::init(['URL' => URL]);

// Define o mapeamento de middlewares
MiddlewareQueue::setMap([
    'maintenance' => \App\Core\Http\Middlewares\Maintenance::class,
    'require-admin-logout' => \App\Core\Http\Middlewares\RequireAdminLogout::class,
    'require-admin-login' => \App\Core\Http\Middlewares\RequireAdminLogin::class,
    'api' => \App\Core\Http\Middlewares\Api::class,
    'user-basic-auth' => \App\Core\Http\Middlewares\UserBasicAuth::class,
    'jwt-auth' => \App\Core\Http\Middlewares\JWTAuth::class,
    'cache' => \App\Core\Http\Middlewares\Cache::class,
]);

// Define o mapeamento de middlewares padrões (em todas as rotas)
MiddlewareQueue::setDefault([
    'maintenance'
]);

// Retorna o container para uso posterior, se necessário
return Container::class;