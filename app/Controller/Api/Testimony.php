<?php

namespace App\Controller\Api;

use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;
class Testimony extends Api{
    /**
     * Método responsável por retornar os depoimentos
     * @param \App\Http\Request $request
     * @return array
     */
    public static function getTestimonies($request){
        return [
            'depoimentos' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     * @param \App\Http\Request $request
     * @param integer $id
     * @return array
     */
    public static function getTestimony($request, $id){

        if(!is_numeric($id)){
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //valida se existe

        if(!$obTestimony instanceof EntityTestimony){
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        //retorna os detalhes do depoimento
        return  [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data
            ];
    }

     private static function getTestimonyItems($request, &$obPagination)
    {
        $itens = [];
        $quantidadeTotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) qtd')->fetchObject()->qtd;


        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

    
        while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            $itens[] = [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data
            ];
        }


        return $itens;
    }
  
}