<?php

namespace App\Controller\site;

use App\Core\Database\Pagination;

class Page
{
   public static function getPagination($request, $obPagination, $maxPages = 7)
   {
      // URL base da página atual
      $url = $request->getRouter()->getCurrentUrl();
      

      return $obPagination->getPagination($url, 'page', $maxPages);
   }
}
