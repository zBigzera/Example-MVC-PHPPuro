<?php

namespace App\Controller\Admin;

use App\Http\Request;
use \App\Utils\View;
use \App\Model\Entity\User as Entity;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{
    private static function getUserItems($request, &$obPagination)
    {
        $itens = '';
        $quantidadeTotal = Entity::getUsers(null, null, null, 'COUNT(*) qtd')->fetchObject()->qtd;


        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);
        $results = Entity::getUsers(null, 'id DESC', $obPagination->getLimit());

        // Renderiza o item.
        // O método fetchObject(Entity::class) percorre cada linha retornada do banco,
        // criando um novo objeto da classe Entity com os dados da linha atual.
        // Cada propriedade pública da classe Entity é preenchida com os valores correspondentes do banco.
        // O loop while continua até que todas as linhas sejam processadas.
        while ($obUser = $results->fetchObject(Entity::class)) {
            $itens .= View::render("admin/modules/users/item", [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email,
            ]);
        }
        //   $itens = View::render("admin/modules/users/item",[
        //     'item' => $results->fetchObject(Entity::class)       ]);


        return $itens;
    }
    public static function getUsers($request)
    {

        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Usuários', $content, 'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuário
     * @param \App\Http\Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        $content = View::render('admin/modules/users/form', [
            'title' => 'Cadastrar usuário',
            'nome' => '',
            'email' => '',
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Cadastrar usuário', $content, 'users');
    }

      /**
     * Método responsável por cadastrar um novo usuário
     * @param \App\Http\Request $request
     */
    public static function setNewUser($request)
    {

        $postVars = $request->getPostVars();

        $email = $postVars['email'];

        //Verifica se o e-mail ja existe
        $obUser = Entity::getUserByEmail($email);
        if($obUser instanceof Entity){
            $request->getRouter()->redirect('/admin/users/new/?status=duplicated');
        }
        //nova instância de usuário
        
        $obUser = new Entity;
        $obUser->nome = $postVars['nome'] ?? '';
        $obUser->email = $postVars['email'] ?? '';
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT) ?? '';
        $obUser->cadastrar();
        

        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
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
                return Alert::getSuccess('Usuário criado com sucesso!');
            }
            case 'updated':{
                return Alert::getSuccess("Usuário editado com sucesso!");
            }
            case 'deleted':{
                return Alert::getSuccess("Usuário deletado com sucecsso!");
            }
            case 'duplicated':{
                return Alert::getError("O e-mail digitado já esta sendo utilizado por outro usuário.");
            }
        }

        return '';
    }
     /**
     * Método responsável por retornar o formulário de edição de um usuário
     * @param \App\Http\Request $request
     * @param integer $id
     * @return string
     */
     public static function getEditUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = Entity::getUserById($id);

        if(!$obUser instanceof Entity){
            $request->getRouter()->redirect('/admin/users');
        }
      
        $content = View::render('admin/modules/users/form', [
            'title' => 'Editar usuário',
            'nome' => $obUser->nome,
            'email'=> $obUser->email,
            'status' => self::getStatus($request)
        ]);

        return parent::getPanel('Editar usuário', $content, 'users');
    }

    /**
     * Método responsável por atualizar um usuário
     * @param \App\Http\Request $request
     * @param integer $id
     */
     public static function setEditUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = Entity::getUserById($id);

        if(!$obUser instanceof Entity){
            $request->getRouter()->redirect('/admin/users');
        }
      
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $obUserMail = Entity::getUserByEmail($email);
        if($obUserMail instanceof Entity && $obUserMail->id != $id){
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }
        //atualiza a instância
        $obUser->nome = $postVars['nome'] ?? $obUser->nome;
        $obUser->email = $postVars['email'] ?? $obUser->email;
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT) ?? $obUser->senha;

        $obUser->atualizar();

        $request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
    }


     /**
     * Método responsável retornar o form de exclusão de um usuário
     * @param \App\Http\Request $request
     * @param integer $id
     * @return string
     */
     public static function getDeleteUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = Entity::getUserById($id);

        if(!$obUser instanceof Entity){
            $request->getRouter()->redirect('/admin/users');
        }
      
        $content = View::render('admin/modules/users/delete', [
            'nome' => $obUser->nome,
            'email'=> $obUser->email,
        ]);

        return parent::getPanel('Excluir usuário', $content, 'users');
    }

     /**
     * Método responsável por excluir um usuário
     * @param \App\Http\Request $request
     * @param integer $id
     */
     public static function setDeleteUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = Entity::getUserById($id);

        if(!$obUser instanceof Entity){
            $request->getRouter()->redirect('/admin/users');
        }

        $obUser->excluir();

        $request->getRouter()->redirect('/admin/users/?status=deleted');
    }
}
