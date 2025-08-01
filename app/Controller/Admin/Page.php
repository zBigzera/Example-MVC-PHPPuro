<?php

namespace App\Controller\Admin;
use App\Core\View;
class Page{
    /**
     * Módulos disponíveis no painel
     * @var array
     */
    public static $modules =[
        'home' => ['label' => 'Início', 'link' => URL.'/admin'],
        'testimonies' => ['label' => 'Depoimentos', 'link' => URL.'/admin/testimonies'],
        'users' => ['label' => 'Usuários', 'link' => URL.'/admin/users']
    ];

    /**
     * Método responsável por retornar os dados do menu
     * @param string $currentModule
     * @return array
     */
   private static function getMenu(?string $currentModule = null): array {
        $links = [];
        foreach (self::$modules as $key => $module) {
            $links[] = [
                'label' => $module['label'],
                'link' => $module['link'],
                'current' => $key === $currentModule,
            ];
        }
        return $links;
    }

    /**
     * Método responsável por criar a renderização com o menu
     * @param string $view
     * @param array $vars
     * @param mixed $currentModule
     * @return string
     */
    public static function render(string $view, array $vars = [], ?string $currentModule = null): string {

      $vars['menu'] = self::getMenu($currentModule);
    
    return View::render($view, $vars);
}
   
}