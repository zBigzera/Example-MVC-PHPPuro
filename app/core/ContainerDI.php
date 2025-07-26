<?php

namespace App\Core;

use DI\ContainerBuilder;
use PDO;
use App\Core\Database\Database;

class ContainerDI
{
    public static function buildContainer()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            PDO::class => function () {
                $config = [
                    'host' => getenv('DB_HOST') ?: 'localhost',
                    'name' => getenv('DB_NAME') ?: 'mvc_pure_php',
                    'user' => getenv('DB_USER') ?: 'root',
                    'pass' => getenv('DB_PASS') ?: '',
                    'port' => getenv('DB_PORT') ?: 3306
                ];

                try {
                    $pdo = new PDO(
                        "mysql:host={$config['host']};dbname={$config['name']};port={$config['port']}",
                        $config['user'],
                        $config['pass']
                    );
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    return $pdo;
                } catch (\PDOException $e) {
                    die('Database Connection Error: ' . $e->getMessage());
                }
            },

            Database::class => \DI\create(Database::class)->constructor( \DI\get(PDO::class), null),
        ]);

        return $containerBuilder->build();
    }
}


