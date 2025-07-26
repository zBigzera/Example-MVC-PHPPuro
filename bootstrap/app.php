<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Core\ContainerDI;
use App\Core\View;
use App\Core\Http\Middlewares\QueueMiddleware as MiddlewareQueue;
use App\Core\Http\Middlewares\Map as Map;

// Carrega as variáveis de ambiente
$envFile = require __DIR__ . "/../env.php";

foreach ($envFile as $key => $value) {
    putenv("$key=$value");
}

// Cria o container PHP-DI
$container = ContainerDI::buildContainer();

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

require __DIR__ . '/../app/core/http/middlewares/Map.php';

// Define o mapeamento de middlewares
MiddlewareQueue::setMap(Map::$middlewares);

// Define o mapeamento de middlewares padrões (em todas as rotas)
MiddlewareQueue::setDefault(Map::$default);


// Retorna o container para uso posterior, se necessário
return $container;