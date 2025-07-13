<?php

namespace App\Controller\Admin;
use \App\Core\View;
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
   
   public static function getPagination($request, $obPagination, $maxPages = 3)
   {
      $pages = $obPagination->getPages();

      if (count($pages) <= 1)
         return null;

      $totalPages = count($pages);

      $currentPage = null;
      foreach ($pages as $page) {
         if ($page['current']) {
            $currentPage = $page['page'];
            break;
         }
      }

      $url = $request->getRouter()->getCurrentUrl();
      $queryParams = $request->getQueryParams();

      $pageLinks = [];

      $buildLink = function ($pageNum) use ($url, $queryParams) {
         $queryParams['page'] = $pageNum;
         return $url . '?' . http_build_query($queryParams);
      };

      // Voltar <<
      if ($currentPage > 1) {
         $pageLinks[] = [
            'page' => '<<',
            'link' => $buildLink($currentPage - 1),
            'active' => false,
         ];
      }

      // Primeira página
      $pageLinks[] = [
         'page' => 1,
         'link' => $buildLink(1),
         'active' => $currentPage == 1,
      ];

      // Intervalo páginas do meio
      $start = max(2, $currentPage - floor($maxPages / 2));
      $end = min($totalPages - 1, $start + $maxPages - 1);
      $start = max(2, $end - $maxPages + 1);

      // Pontinhos antes do meio
      if ($start > 2) {
         $pageLinks[] = [
            'page' => '...',
            'link' => null,
            'disabled' => true,
         ];
      }

      // Páginas do meio
      for ($i = $start; $i <= $end; $i++) {
         $pageLinks[] = [
            'page' => $i,
            'link' => $buildLink($i),
            'active' => $i == $currentPage,
         ];
      }

      // Pontinhos depois do meio
      if ($end < $totalPages - 1) {
         $pageLinks[] = [
            'page' => '...',
            'link' => null,
            'disabled' => true,
         ];
      }

      // Última página
      if ($totalPages > 1) {
         $pageLinks[] = [
            'page' => $totalPages,
            'link' => $buildLink($totalPages),
            'active' => $currentPage == $totalPages,
         ];
      }

      // Avançar >>
      if ($currentPage < $totalPages) {
         $pageLinks[] = [
            'page' => '>>',
            'link' => $buildLink($currentPage + 1),
            'active' => false,
         ];
      }

      return $pageLinks;
   }
}