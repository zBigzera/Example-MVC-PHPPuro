<?php

namespace App\Controller\Pages;

use \App\Utils\View;
class Page
{

   /**
    * Método responsável por renderizar o topo da página
    * @return string
    */
   private static function getHeader()
   {
      return View::render('pages/header');
   }

   /**
    * Método responsável por renderizar o topo da página
    * @return string
    */
   private static function getFooter()
   {
      return View::render('pages/footer');
   }

   public static function getPagination($request, $obPagination)
   {
      $pages = $obPagination->getPages();

      if (count($pages) <= 1)
         return '';

      $totalPages = count($pages);

      // Quantidade máxima de páginas do meio (além da primeira e última)
      $maxPagesToShow = 3;

      // Pega a página atual
      $currentPage = null;
      foreach ($pages as $page) {
         if ($page['current']) {
            $currentPage = $page['page'];
            break;
         }
      }

      $url = $request->getRouter()->getCurrentUrl();
      $queryParams = $request->getQueryParams();

      $links = '';

      // Botão Voltar <<
      $prevPage = max($currentPage - 1, 1);
      $queryParams['page'] = $prevPage;
      $linkPrev = $url . '?' . http_build_query($queryParams);
      $links .= View::render("pages/pagination/link", [
         'page' => '<<',
         'link' => $linkPrev,
         'active' => ''
      ]);

      // Primeira página sempre
      $queryParams['page'] = 1;
      $linkFirst = $url . '?' . http_build_query($queryParams);
      $active = ($currentPage == 1) ? 'active' : '';
      $links .= View::render("pages/pagination/link", [
         'page' => 1,
         'link' => $linkFirst,
         'active' => $active
      ]);

      // Calcula intervalo das páginas do meio
      $start = max(2, $currentPage - floor($maxPagesToShow / 2));
      $end = min($totalPages - 1, $start + $maxPagesToShow - 1);

      // Ajusta start se o final foi cortado
      $start = max(2, $end - $maxPagesToShow + 1);

      // Pontinhos antes do intervalo do meio
      if ($start > 2) {
         $links .= '<span class="page-link disabled">...</span>';
      }

      // Páginas do meio
      for ($i = $start; $i <= $end; $i++) {
         $queryParams['page'] = $i;
         $link = $url . '?' . http_build_query($queryParams);
         $active = ($i == $currentPage) ? 'active' : '';
         $links .= View::render("pages/pagination/link", [
            'page' => $i,
            'link' => $link,
            'active' => $active
         ]);
      }

      // Pontinhos depois do intervalo do meio
      if ($end < $totalPages - 1) {
         $links .= '<span class="page-link disabled">...</span>';
      }

      // Última página sempre
      if ($totalPages > 1) {
         $queryParams['page'] = $totalPages;
         $linkLast = $url . '?' . http_build_query($queryParams);
         $active = ($currentPage == $totalPages) ? 'active' : '';
         $links .= View::render("pages/pagination/link", [
            'page' => $totalPages,
            'link' => $linkLast,
            'active' => $active
         ]);
      }

      // Botão Avançar >>
      $nextPage = min($currentPage + 1, $totalPages);
      $queryParams['page'] = $nextPage;
      $linkNext = $url . '?' . http_build_query($queryParams);
      $links .= View::render("pages/pagination/link", [
         'page' => '>>',
         'link' => $linkNext,
         'active' => ''
      ]);

      return View::render("pages/pagination/box", [
         'links' => $links
      ]);
   }



   /**
    * Método responsável por retornar o conteúdo (view) da página genérica
    * @return string
    */
   public static function getPage($title, $content, $header = true, $footer = true)
   {
      return View::render("pages/page", [
         "title" => $title,
         "header" => $header ? self::getHeader() : '',
         "footer" => $footer ? self::getFooter() : '',
         "content" => $content
      ]);
   }

}