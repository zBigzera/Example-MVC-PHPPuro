<?php

namespace App\Core\Http\Middlewares;

class Map {

    /**
     * Mapeamento de middlewares
     * @var array
     */
    public static $middlewares = [
        'maintenance' => \App\Core\Http\Middlewares\Maintenance::class,
        'require-admin-logout' => \App\Core\Http\Middlewares\RequireAdminLogout::class,
        'require-admin-login' => \App\Core\Http\Middlewares\RequireAdminLogin::class,
        'api' => \App\Core\Http\Middlewares\Api::class,
        'user-basic-auth' => \App\Core\Http\Middlewares\UserBasicAuth::class,
        'jwt-auth' => \App\Core\Http\Middlewares\JWTAuth::class,
        'cache' => \App\Core\Http\Middlewares\Cache::class
    ];

    /**
     * Middlewares padrões (executados em todas as rotas)
     * @var array
     */
    public static $default = [
        'maintenance'
    ];

}

