<?php

namespace App\Controller\Site;

use App\Core\View;
use App\Model\Dto\OrganizationDTO;

class About{
    private OrganizationDTO $organization;

    public function __construct(OrganizationDTO $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Método responsável por retornar o conteúdo (view) de sobre
     * @return string
     */
    public function getAbout(){
         return View::render("site/pages/about.twig", [
            "title" => "Sobre",
            "name" => $this->organization->name,
            "description" => $this->organization->description,
            "URL" => URL
        ]);
    }
}