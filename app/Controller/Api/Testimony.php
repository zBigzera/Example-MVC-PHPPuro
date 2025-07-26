<?php

namespace App\Controller\Api;

use App\Model\Entity\Testimony as EntityTestimony;
use App\Core\Database\Pagination;
class Testimony extends Api{

    private $testimonyEntity;

    public function __construct(EntityTestimony $testimony) {
        $this->testimonyEntity = $testimony;
    }

    /**
     * Método responsável por retornar os depoimentos
     * @param \App\Core\Http\Request $request
     * @return array
     */
    public function getTestimonies($request){
        return [
            'depoimentos' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     * @param \App\Core\Http\Request $request
     * @param integer $id
     * @return array
     */
    public function getTestimony($request, $id){

        if(!is_numeric($id)){
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }
        $obTestimony = $this->testimonyEntity->getTestimonyById($id);

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

     private function getTestimonyItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $quantidadeTotal = $this->testimonyEntity->count();

        $obPagination = new Pagination($paginaAtual, 5, $quantidadeTotal);
        
         $results = $this->testimonyEntity->getTestimonies(null, "id DESC", $obPagination->getLimit());

        $itens = [];
        foreach ($results as $testimonyData) {
            $obTestimony = $this->testimonyEntity->hydrate($testimonyData);
            $itens[] = [
                "id" => (int)$obTestimony->id,
                "nome" => $obTestimony->nome,
                "mensagem" => $obTestimony->mensagem,
                "data" => $obTestimony->data
            ];
        }


        return $itens;
    }

    /**
     * Método responsável por cadastrar um novo depoimento
     * @param \App\Core\Http\Request $request
     */
    public function setNewTestimony($request){
        $postVars = $request->getQueryParams();

        //valida os campos obrigatorios

        if(!isset($postVars['nome']) || !isset($postVars['mensagem'])){
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        //novo depoimento
        $this->testimonyEntity->nome = $postVars['nome'];
        $this->testimonyEntity->mensagem = $postVars['mensagem'];
        $this->testimonyEntity->cadastrar();

        //retorna os detalhes do depoimento cadastrado
        return  [
                'id' => (int)$this->testimonyEntity->id,
                'nome' => $this->testimonyEntity->nome,
                'mensagem' => $this->testimonyEntity->mensagem,
                'data' => $this->testimonyEntity->data
            ];
    }

     /**
     * Método responsável por  alterar um depoimento
     * @param \App\Core\Http\Request $request
     */
    public function setEditTestimony($request, $id){
        $postVars = $request->getQueryParams();

        //valida os campos obrigatorios

        if(!isset($postVars['nome']) || !isset($postVars['mensagem'])){
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        //buscar o depoimento

        $obTestimony = $this->testimonyEntity->getTestimonyById($id);

        if(!$obTestimony instanceof EntityTestimony){
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }


        //novo depoimento
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->atualizar();

        //retorna os detalhes do depoimento atualizado
        return  [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data
            ];
    }
  

    /**
     * Método responsável por excluir um depoimento
     * @param \App\Core\Http\Request $request
     */
    public function setDeleteTestimony($request, $id){

        //buscar o depoimento

        $obTestimony = $this->testimonyEntity->getTestimonyById($id);

        if(!$obTestimony instanceof EntityTestimony){
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }


        $obTestimony->excluir();

        //retorna os detalhes do depoimento atualizado
        return  [
                'sucesso' => true
            ];
    }
}