<?php

namespace App\Controller\Admin;

use App\Core\Http\Request;
use App\Model\Entity\Testimony as Entity;
use App\Core\Database\Pagination;

class Testimony extends Page
{
     private $testimonyEntity;

    public function __construct(Entity $testimonyEntity)
    {
        $this->testimonyEntity = $testimonyEntity;
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
                "id" => $obTestimony->id,
                "nome" => $obTestimony->nome,
                "mensagem" => $obTestimony->mensagem,
                "data" => date("d/m/Y H:i:s", strtotime($obTestimony->data))
            ];
        }

        return $itens;
    }
    public function getTestimonies($request)
    {
        return parent::render('admin/pages/testimonies/index.twig', [
            'title' => 'Depoimentos',
            'itens' => $this->getTestimonyItems($request, $obPagination),
            'pagination' => $obPagination->getPagination($request->getFullUrl(),'page'),
            'status' => self::getStatus($request)
        ], 'testimonies');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     * @param \App\Core\Http\Request $request
     * @return string
     */
    public function getNewTestimony($request)
    {
        return parent::render('admin/pages/testimonies/form.twig', [
            'title' => 'Cadastrar depoimento',
            'nome' => '',
            'mensagem' => '',
            'status' => ''
        ], 'testimonies');

    }

      /**
     * Método responsável por cadastrar um novo depoimento
     * @param \App\Core\Http\Request $request
     */
    public function setNewTestimony($request)
    {

        $postVars = $request->getPostVars();

        //nova instância de depoimento

        $this->testimonyEntity->nome = $postVars['nome'] ?? '';
        $this->testimonyEntity->mensagem = $postVars['mensagem'] ?? '';
         $this->testimonyEntity->cadastrar();


        $request->getRouter()->redirect('/admin/testimonies/'. $this->testimonyEntity->id.'/edit?status=created');
    }


    /**
     * Método responsável por retonar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request){
        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status']){
            case 'created': { 
                return Alert::getSuccess('Depoimento criado com sucesso!');
            }
            case 'updated':{
                return Alert::getSuccess("Depoimento editado com sucesso!");
            }
            case 'deleted':{
                return Alert::getSuccess("Depoimento deletado com sucecsso!");
            }
        }

        return '';
    }
     /**
     * Método responsável por retornar o formulário de edição de um depoimento
     * @param \App\Core\Http\Request $request
     * @param integer $id
     * @return string
     */
     public function getEditTestimony($request, $id)
    {
        //Obtém o depoimento do DB
      
        $obTestimony =  $this->testimonyEntity->getTestimonyById($id);
 
        if(!$obTestimony instanceof Entity){
            $request->getRouter()->redirect('/admin/testimonies');
        }
      
        return parent::render('admin/pages/testimonies/form.twig', [
            'title' => 'Editar depoimento',
            'nome' => $obTestimony->nome,
            'mensagem'=> $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ], 'testimonies');

    }

    /**
     * Método responsável por atualizar um depoimento
     * @param \App\Core\Http\Request $request
     * @param integer $id
     */
     public function setEditTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony =  $this->testimonyEntity->getTestimonyById($id);
        if(!$obTestimony instanceof Entity){
            $request->getRouter()->redirect('/admin/testimonies');
        }
      
        $postVars = $request->getPostVars();

        //atualiza a instância
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;

        $obTestimony->atualizar();

        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=updated');
    }


     /**
     * Método responsável retornar o form de exclusão de um depoimento
     * @param \App\Core\Http\Request $request
     * @param integer $id
     * @return string
     */
     public function getDeleteTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony =  $this->testimonyEntity->getTestimonyById($id);

        if(!$obTestimony instanceof Entity){
            $request->getRouter()->redirect('/admin/testimonies');
        }
      
      return parent::render('admin/pages/testimonies/delete.twig', [
            'nome' => $obTestimony->nome,
            'mensagem'=> $obTestimony->mensagem,
        ], 'testimonies');

    }

     /**
     * Método responsável por excluir um depoimento
     * @param \App\Core\Http\Request $request
     * @param integer $id
     */
     public function setDeleteTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony =  $this->testimonyEntity->getTestimonyById($id);

        if(!$obTestimony instanceof Entity){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        $obTestimony->excluir();

        $request->getRouter()->redirect('/admin/testimonies/?status=deleted');
    }
}