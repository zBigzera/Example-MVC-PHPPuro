<?php

namespace App\Controller\Admin;

use App\Core\Http\Request;
use \App\Model\Entity\Testimony as Entity;
use \App\Core\Database\Pagination;

class Testimony extends Page
{
    private static function getTestimonyItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $obTestimonyEntity = new Entity();
        $quantidadeTotal = $obTestimonyEntity->count();

        $obPagination = new Pagination($paginaAtual, 5, $quantidadeTotal);
        $results = $obTestimonyEntity->findAll(null, "id DESC", $obPagination->getLimit());

        $itens = [];
        foreach ($results as $testimonyData) {
            $obTestimony = Entity::hydrate($testimonyData);
            $itens[] = [
                "id" => $obTestimony->id,
                "nome" => $obTestimony->nome,
                "mensagem" => $obTestimony->mensagem,
                "data" => date("d/m/Y H:i:s", strtotime($obTestimony->data))
            ];
        }

        return $itens;
    }
    public static function getTestimonies($request)
    {
        return parent::render('admin/pages/testimonies/index.twig', [
            'title' => 'Depoimentos',
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ], 'testimonies');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     * @param \App\Core\Http\Request $request
     * @return string
     */
    public static function getNewTestimony($request)
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
    public static function setNewTestimony($request)
    {

        $postVars = $request->getPostVars();

        //nova instância de depoimento

        $obTestimony = new Entity;
        $obTestimony->nome = $postVars['nome'] ?? '';
        $obTestimony->mensagem = $postVars['mensagem'] ?? '';
        $obTestimony->cadastrar();
        

        $request->getRouter()->redirect('/admin/testimonies/'.$obTestimony->id.'/edit?status=created');
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
     public static function getEditTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony = Entity::getTestimonyById($id);

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
     public static function setEditTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony = Entity::getTestimonyById($id);

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
     public static function getDeleteTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony = Entity::getTestimonyById($id);

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
     public static function setDeleteTestimony($request, $id)
    {
        //Obtém o depoimento do DB
        $obTestimony = Entity::getTestimonyById($id);

        if(!$obTestimony instanceof Entity){
            $request->getRouter()->redirect('/admin/testimonies');
        }

        $obTestimony->excluir();

        $request->getRouter()->redirect('/admin/testimonies/?status=deleted');
    }
}
