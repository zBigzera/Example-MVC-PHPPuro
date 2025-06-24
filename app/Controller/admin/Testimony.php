<?php

namespace App\Controller\Admin;

use App\Http\Request;
use \App\Utils\View;
use \App\Model\Entity\Testimony as Entity;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{
    private static function getTestimonyItems($request, &$obPagination)
    {
        $itens = '';
        $quantidadeTotal = Entity::getTestimonies(null, null, null, 'COUNT(*) qtd')->fetchObject()->qtd;


        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);
        $results = Entity::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item.
        // O método fetchObject(Entity::class) percorre cada linha retornada do banco,
        // criando um novo objeto da classe Entity com os dados da linha atual.
        // Cada propriedade pública da classe Entity é preenchida com os valores correspondentes do banco.
        // O loop while continua até que todas as linhas sejam processadas.
        while ($obTestimony = $results->fetchObject(Entity::class)) {
            $itens .= View::render("admin/modules/testimonies/item", [
                'id' => $obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date("d/m/Y H:i:s", strtotime($obTestimony->data))
            ]);
        }
        //   $itens = View::render("admin/modules/testimonies/item",[
        //     'item' => $results->fetchObject(Entity::class)       ]);


        return $itens;
    }
    public static function getTestimonies($request)
    {

        $content = View::render('admin/modules/testimonies/index', [
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Depoimentos', $content, 'testimonies');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     * @param \App\Http\Request $request
     * @return string
     */
    public static function getNewTestimony($request)
    {
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Cadastrar depoimento',
            'nome' => '',
            'mensagem' => '',
            'status' => ''
        ]);

        return parent::getPanel('Cadastrar depoimento', $content, 'testimonies');
    }

      /**
     * Método responsável por cadastrar um novo depoimento
     * @param \App\Http\Request $request
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
     * @param \App\Http\Request $request
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
      
        $content = View::render('admin/modules/testimonies/form', [
            'title' => 'Editar depoimento',
            'nome' => $obTestimony->nome,
            'mensagem'=> $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Editar depoimento', $content, 'testimonies');
    }

    /**
     * Método responsável por atualizar um depoimento
     * @param \App\Http\Request $request
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
     * @param \App\Http\Request $request
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
      
        $content = View::render('admin/modules/testimonies/delete', [
            'nome' => $obTestimony->nome,
            'mensagem'=> $obTestimony->mensagem,
        ]);

        return parent::getPanel('Excluir depoimento', $content, 'testimonies');
    }

     /**
     * Método responsável por excluir um depoimento
     * @param \App\Http\Request $request
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
