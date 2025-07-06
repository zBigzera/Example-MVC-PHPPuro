<?php

namespace App\Controller\Api;

use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;
class User extends Api{
    /**
     * Método responsável por retornar os usuários
     * @param \App\Http\Request $request
     * @return array
     */
    public static function getUsers($request){
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }


    /**
     * Método responsável por retornar o usuário atualmente conectado
     * @param \App\Http\Request $request
     * @return array
     */
    public static function getCurrentUser($request){
        
        $obUser = $request->user;

        return  [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um usuário
     * @param \App\Http\Request $request
     * @param integer $id
     * @return array
     */
    public static function getUser($request, $id){

        if(!is_numeric($id)){
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }
        $obUser = EntityUser::getUserById($id);

        //valida se existe

        if(!$obUser instanceof EntityUser){
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }

        //retorna os detalhes do usuário
        return  [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ];
    }

     private static function getUserItems($request, &$obPagination)
    {
        $itens = [];
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) qtd')->fetchObject()->qtd;


        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

    
        while ($obUser = $results->fetchObject(EntityUser::class)) {
            $itens[] = [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ];
        }


        return $itens;
    }

    /**
     * Método responsável por cadastrar um novo usuário
     * @param \App\Http\Request $request
     */
    public static function setNewUser($request){
        $postVars = $request->getPostVars();

        //valida os campos obrigatorios

        if(!isset($postVars['nome']) || !isset($postVars['email']) || !isset($postVars['senha'])){
            throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
        }

        //novo usuário
        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);

        if($obUserEmail instanceof EntityUser){
            throw new \Exception("O e-mail '".$postVars['email']."' já esta em uso", 400);
        }


        $obUser = new EntityUser;
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //retorna os detalhes do usuário cadastrado
        return  [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email,
                'senha' => $obUser->senha
            ];
    }

     /**
     * Método responsável por  alterar um usuário
     * @param \App\Http\Request $request
     */
    public static function setEditUser($request, $id){
        $postVars = $request->getPostVars();

        //valida os campos obrigatorios

        if(!isset($postVars['nome']) || !isset($postVars['email'])){
            throw new \Exception("Os campos 'nome' e 'email' são obrigatórios", 400);
        }

        //buscar o usuário

        $obUser = EntityUser::getUserById($id);

        if(!$obUser instanceof EntityUser){
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }

        $obUserEmail = EntityUser::getUserByEmail($postVars['email']);

        if($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id){
            throw new \Exception("O e-mail '".$postVars['email']."' já esta em uso", 400);
        }


        //novo usuário
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->atualizar();

        //retorna os detalhes do usuário atualizado
        return  [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email,
            ];
    }
  

    /**
     * Método responsável por excluir um usuário
     * @param \App\Http\Request $request
     */
    public static function setDeleteUser($request, $id){

        //buscar o usuário

        $obUser = EntityUser::getUserById($id);

        if(!$obUser instanceof EntityUser){
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }


        $obUser->excluir();

        //retorna os detalhes do usuário atualizado
        return  [
                'sucesso' => true
            ];
    }
}