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
      $pages = $obPagination->getPageRange($maxPages);

      if ($obPagination->getTotalPages() <= 1)
         return null;

      $url = $request->getRouter()->getCurrentUrl();
      $queryParams = $request->getQueryParams();

      $pageLinks = [];

      $buildLink = function ($pageNum) use ($url, $queryParams) {
         $queryParams["page"] = $pageNum;
         return $url . "?" . http_build_query($queryParams);
      };

      // Voltar <<
      if ($obPagination->hasPreviousPage()) {
         $pageLinks[] = [
            "page" => "<<",
            "link" => $buildLink($obPagination->getPreviousPage()),
            "active" => false,
         ];
      }

      // Primeira página
      if (!in_array(1, $pages)) {
         $pageLinks[] = [
            "page" => 1,
            "link" => $buildLink(1),
            "active" => $obPagination->getCurrentPage() == 1,
         ];
         if ($obPagination->getCurrentPage() > ($maxPages + 1)) {
            $pageLinks[] = [
               "page" => "...",
               "link" => null,
               "disabled" => true,
            ];
         }
      }

      // Páginas do meio
      foreach ($pages as $page) {
         $pageLinks[] = [
            "page" => $page,
            "link" => $buildLink($page),
            "active" => $page == $obPagination->getCurrentPage(),
         ];
      }

      // Pontinhos depois do meio
      if ($obPagination->getCurrentPage() < ($obPagination->getTotalPages() - $maxPages)) {
         $pageLinks[] = [
            "page" => "...",
            "link" => null,
            "disabled" => true,
         ];
      }

      // Última página
      if (!in_array($obPagination->getTotalPages(), $pages) && $obPagination->getTotalPages() > 1) {
         $pageLinks[] = [
            "page" => $obPagination->getTotalPages(),
            "link" => $buildLink($obPagination->getTotalPages()),
            "active" => $obPagination->getCurrentPage() == $obPagination->getTotalPages(),
         ];
      }

      // Avançar >>
      if ($obPagination->hasNextPage()) {
         $pageLinks[] = [
            "page" => ">>",
            "link" => $buildLink($obPagination->getNextPage()),
            "active" => false,
         ];
      }

      return $pageLinks;
   }
}