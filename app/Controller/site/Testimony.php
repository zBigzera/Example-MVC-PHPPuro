<?php

namespace App\Controller\Site;

use App\Core\View;
use App\Model\Entity\Testimony as Entity;
use App\Core\Database\Pagination;
class Testimony{

    private $testimonyEntity;

    public function __construct(Entity $entity) {
        $this->testimonyEntity = $entity;
    }

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @return array
     */
    private function getTestimonyItems($request, &$obPagination){
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $quantidadeTotal = $this->testimonyEntity->count();

        $obPagination = new Pagination($paginaAtual, 2, $quantidadeTotal);
        
         $results = $this->testimonyEntity->getTestimonies(null, "id DESC", $obPagination->getLimit());
        
        $itens = [];
        foreach($results as $testimonyData) {
            $obTestimony = $this->testimonyEntity->hydrate($testimonyData);
            $itens[] = [ 
           "nome" => $obTestimony->nome,
           "mensagem" => $obTestimony->mensagem,
           "data" => date("d/m/Y H:i:s", strtotime($obTestimony->data))
        ];
        }

        return $itens;
    }

    /**
     * Método responsável por retornar o conteúdo (view)
     * @return string
     */
    public function getTestimonies($request){

        return View::render("site/pages/testimonies/index.twig",[ 
            'title' => 'Depoimentos',
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => $obPagination->getPagination($request->getFullUrl(),'page')
        ]);
    }

    public function insertTestimony($request){
       
        $postVars = $request->getPostVars();
         
        $this->testimonyEntity->nome = $postVars['nome'];
        $this->testimonyEntity->mensagem = $postVars['mensagem'];

        $this->testimonyEntity->cadastrar();
        return $request->getRouter()->redirect('/depoimentos');
    }
}