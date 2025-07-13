<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class View
{
    private static $twig;

    public static function init($globals = [])
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../resources/view');

        $isDev = ($_SERVER['ENVIRONMENT'] ?? '') === 'development';

        self::$twig = new Environment($loader, [
            'cache' => $isDev ? false : __DIR__ . '/../../cache/twig',
            'debug' => $isDev,
            'autoescape' => 'html',
            'auto_reload' => $isDev,
        ]);

        if ($isDev) {
            self::$twig->addExtension(new DebugExtension());
        }

        // Variáveis globais para todas as views
        foreach ($globals as $key => $value) {
            self::$twig->addGlobal($key, $value);
        }
    }

    public static function render(string $view, array $vars = []): string
    {
        return self::$twig->render($view . '.html', $vars);
    }
}
