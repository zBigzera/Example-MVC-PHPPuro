<?php

namespace App\Controller\site;

use \App\Core\View;
use \App\Model\Entity\Testimony as Entity;
use \App\Core\Database\Pagination;
class Testimony extends Page{
    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @return array
     */
    private static function getTestimonyItems($request, &$obPagination){
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $obTestimonyEntity = new Entity();
        $quantidadeTotal = $obTestimonyEntity->count();

        $obPagination = new Pagination($paginaAtual, 2, $quantidadeTotal);
        $results = $obTestimonyEntity->findAll(null, "id DESC", $obPagination->getLimit());
        
        $itens = [];
        foreach($results as $testimonyData) {
            $obTestimony = Entity::hydrate($testimonyData);
            $itens[] = [ 
           "nome" => $obTestimony->nome,
           "mensagem" => $obTestimony->mensagem,
           "data" => date("d/m/Y H:i:s", strtotime($obTestimony->data))
        ];
        }

        return $itens;
    }

    // private static function getTestimonyItems(){
    //     $itens = '';
        
    //     $results = Entity::getTestimonies(null,'id DESC');
        
    //    // Renderiza o item.
    ////Nao é recomendável utilizar fetchObjetc por limitar (a classe tem que estar igual ao BD)
    //    $testimoniesArray = $results->fetchAll(\PDO::FETCH_ASSOC);


    //     foreach ($testimoniesArray as $data) {
    //         $itens[] = Entity::fromArray($data);
    //     }

    //     return $itens;
    // }

    /**
     * Método responsável por retornar o conteúdo (view)
     * @return string
     */
    public static function getTestimonies($request){

        return View::render("site/pages/testimonies/index.twig",[ 
            'title' => 'Depoimentos',
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request,$obPagination),
        ]);
    }

    public static function insertTestimony($request){
       
        $postVars = $request->getPostVars();
         
        $obTestimony = new Entity;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];

        $obTestimony->cadastrar();
        return self::getTestimonies($request);
    }
}