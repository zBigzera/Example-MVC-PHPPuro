<?php

namespace App\Controller\site;

use \App\Core\View;
class Home extends Page{
    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @return string
     */
    public static function getHome(){

        return View::render("site/pages/home.twig", [
            "URL" => URL
        ]);

    }
}