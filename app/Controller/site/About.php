<?php

namespace App\Controller\site;

use \App\Core\View;
use \App\Model\Entity\Organization;
class About extends Page{
    /**
     * Método responsável por retornar o conteúdo (view) de sobre
     * @return string
     */
    public static function getAbout(){

        $obOrganization = new Organization;

         return View::render("site/pages/about.twig", [
            "title" => "Sobre",
            "name" => $obOrganization->name,
            "description" => $obOrganization->description,
            "URL" => URL
        ]);
    }
}