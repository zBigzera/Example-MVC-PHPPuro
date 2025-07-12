<?php

namespace App\Controller\site;

class Page
{
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