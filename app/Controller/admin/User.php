<?php

namespace App\Controller\Admin;

use App\Core\Http\Request;
use App\Model\Entity\User as Entity;
use App\Core\Database\Pagination;

class User extends Page
{
    private $obUserEntity;

    public function __construct(Entity $obuser)
    {
        $this->obUserEntity = $obuser;
    }

    private function getUserItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $quantidadeTotal = $this->obUserEntity->count();

        $obPagination = new Pagination($paginaAtual, 5, $quantidadeTotal);

        $results = $this->obUserEntity->getUsers(null, "id DESC", $obPagination->getLimit());

        $itens = [];
        foreach ($results as $userData) {
            $obUser = $this->obUserEntity->hydrate($userData);
            $itens[] = [
                "id" => $obUser->id,
                "nome" => $obUser->nome,
                "email" => $obUser->email,
            ];
        }

        return $itens;
    }
    public function getUsers($request)
    {

        return parent::render('admin/pages/users/index.twig', [
            'title' => 'Usuários',
            'itens' => $this->getUserItems($request, $obPagination),
            'pagination' => $obPagination->getPagination($request->getFullUrl(), 'page'),
            'status' => self::getStatus($request)
        ], 'users');

    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuário
     * @param \App\Core\Http\Request $request
     * @return string
     */
    public function getNewUser($request)
    {
        return parent::render('admin/pages/users/form.twig', [
            'title' => 'Cadastrar usuário',
            'nome' => '',
            'email' => '',
            'status' => self::getStatus($request)
        ], 'users');
    }

    /**
     * Método responsável por cadastrar um novo usuário
     * @param \App\Core\Http\Request $request
     */
    public function setNewUser($request)
    {

        $postVars = $request->getPostVars();

        $email = $postVars['email'];

        //Verifica se o e-mail ja existe
        $obUser = $this->obUserEntity->getUserByEmail($email);
        if ($obUser instanceof Entity) {
            $request->getRouter()->redirect('/admin/users/new/?status=duplicated');
        }
        //nova instância de usuário

        $this->obUserEntity->nome = $postVars['nome'] ?? '';
        $this->obUserEntity->email = $postVars['email'] ?? '';
        $this->obUserEntity->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT) ?? '';
        $this->obUserEntity->cadastrar();


        $request->getRouter()->redirect('/admin/users/' . $this->obUserEntity->id . '/edit?status=created');
    }


    /**
     * Método responsável por retonar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status']))
            return '';

        switch ($queryParams['status']) {
            case 'created': {
                return Alert::getSuccess('Usuário criado com sucesso!');
            }
            case 'updated': {
                return Alert::getSuccess("Usuário editado com sucesso!");
            }
            case 'deleted': {
                return Alert::getSuccess("Usuário deletado com sucecsso!");
            }
            case 'duplicated': {
                return Alert::getError("O e-mail digitado já esta sendo utilizado por outro usuário.");
            }
        }

        return '';
    }
    /**
     * Método responsável por retornar o formulário de edição de um usuário
     * @param \App\Core\Http\Request $request
     * @param integer $id
     * @return string
     */
    public function getEditUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = $this->obUserEntity->getUserById($id);

        if (!$obUser instanceof Entity) {
            $request->getRouter()->redirect('/admin/users');
        }

        return parent::render('admin/pages/users/form.twig', [
            'title' => 'Editar usuário',
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ], 'users');
    }

    /**
     * Método responsável por atualizar um usuário
     * @param \App\Core\Http\Request $request
     * @param integer $id
     */
    public function setEditUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = $this->obUserEntity->getUserById($id);

        if (!$obUser instanceof Entity) {
            $request->getRouter()->redirect('/admin/users');
        }

        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $obUserMail = $this->obUserEntity->getUserByEmail($email);
        if ($obUserMail instanceof Entity && $obUserMail->id != $id) {
            $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=duplicated');
        }
        //atualiza a instância
        $obUser->nome = $postVars['nome'] ?? $obUser->nome;
        $obUser->email = $postVars['email'] ?? $obUser->email;
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT) ?? $obUser->senha;

        $obUser->atualizar();

        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=updated');
    }


    /**
     * Método responsável retornar o form de exclusão de um usuário
     * @param \App\Core\Http\Request $request
     * @param integer $id
     * @return string
     */
    public function getDeleteUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = $this->obUserEntity->getUserById($id);

        if (!$obUser instanceof Entity) {
            $request->getRouter()->redirect('/admin/users');
        }

        return parent::render('admin/pages/users/delete.twig', [
            'nome' => $obUser->nome,
            'email' => $obUser->email,
        ], 'users');
    }

    /**
     * Método responsável por excluir um usuário
     * @param \App\Core\Http\Request $request
     * @param integer $id
     */
    public function setDeleteUser($request, $id)
    {
        //Obtém o usuário do DB
        $obUser = $this->obUserEntity->getUserById($id);

        if (!$obUser instanceof Entity) {
            $request->getRouter()->redirect('/admin/users');
        }

        $obUser->excluir();

        $request->getRouter()->redirect('/admin/users/?status=deleted');
    }
}
