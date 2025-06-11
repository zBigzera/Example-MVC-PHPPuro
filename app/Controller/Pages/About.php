<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
class About extends Page{
    /**
     * Método responsável por retornar o conteúdo (view) de sobre
     * @return string
     */
    public static function getAbout(){

        $obOrganization = new Organization;

        $content = View::render("pages/about",[ 
            "name" => $obOrganization->name,
            "description" => $obOrganization->description
        ]);

        return parent::getPage('Sobre', $content);
    }
}