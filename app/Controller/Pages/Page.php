<?php

namespace App\Controller\Pages;

use \App\Utils\View;
class Page{
   
     /**
      * Método responsável por renderizar o topo da página
      * @return string
      */
     private static function getHeader(){
        return View::render('pages/header');
     }

     /**
      * Método responsável por renderizar o topo da página
      * @return string
      */
     private static function getFooter(){
        return View::render('pages/footer');
     }

     public static function getPagination($request,$obPagination){
       $pages = $obPagination->getPages();

       if(count($pages) <= 1) return '';

         $links = '';

         //url atual sem gets

         $url = $request->getRouter()->getCurrentUrl();

         //get
         $queryParams = $request->getQueryParams();

         //renderiza os links
         foreach($pages as $page){
            //altera a página
            $queryParams['page'] = $page['page'];

            //link
            $link = $url.'?'.http_build_query($queryParams);
             $links .= View::render("pages/pagination/link", [ 
        'page' => $page['page'],
        'link' => $link,
        'active' => $page['current'] ? 'active' : '',
      ]);
         }

        

      //Renderiza box de paginação

      return View::render("pages/pagination/box", [ 
        'links' => $links
      ]);

      
     }
    
    
     /**
     * Método responsável por retornar o conteúdo (view) da página genérica
     * @return string
     */
  public static function getPage($title, $content, $header = true, $footer = true){
    return View::render("pages/page", [ 
        "title" => $title,
        "header" => $header ? self::getHeader() : '',
        "footer" => $footer ? self::getFooter() : '',
        "content" => $content
    ]);
}

}