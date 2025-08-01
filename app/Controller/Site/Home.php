<?php

namespace App\Controller\Site;

use App\Core\View;
class Home{
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