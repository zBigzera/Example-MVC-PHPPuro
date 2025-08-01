<?php

namespace App\Controller\Admin;

use App\Core\Http\Request;
use App\Model\DTO\UserDTO as UserDto;
use App\Model\Service\UserService;
use App\Core\Database\Pagination;

class User extends Page
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    private function getUserItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $itemsPerPage = 5;

        $result = $this->userService->getUsers(null, "id DESC", $paginaAtual, $itemsPerPage);
        
        $obPagination = $result['pagination'];
        $users = $result['data'];

        $itens = [];
        foreach ($users as $userDTO) {
            $itens[] = [
                "id" => $userDTO->id,
                "nome" => $userDTO->nome,
                "email" => $userDTO->email,
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

    public function getNewUser($request)
    {
        return parent::render('admin/pages/users/form.twig', [
            'title' => 'Cadastrar usuário',
            'nome' => '',
            'email' => '',
            'status' => self::getStatus($request)
        ], 'users');
    }

    public function setNewUser($request)
    {
        $postVars = $request->getPostVars();

        // Verifica duplicidade
        if ($this->userService->getUserByEmail($postVars['email'] ?? '') instanceof UserDto) {
            $request->getRouter()->redirect('/admin/users/new/?status=duplicated');
        }

        $senha = password_hash($postVars['senha'] ?? '', PASSWORD_DEFAULT);

        $userDTO = UserDto::fromArray([
                    'id' => null,
                    'nome' => $postVars['nome'] ?? '',
                    'email' => $postVars['email'] ?? '',
                    'senha' => $senha
                ]);
        $user = $this->userService->createUser($userDTO);
              
        $request->getRouter()->redirect('/admin/users/' . $user . '/edit?status=created');
    }

    private static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status']))
            return '';

        switch ($queryParams['status']) {
            case 'created': return Alert::getSuccess('Usuário criado com sucesso!');
            case 'updated': return Alert::getSuccess("Usuário editado com sucesso!");
            case 'deleted': return Alert::getSuccess("Usuário deletado com sucesso!");
            case 'duplicated': return Alert::getError("O e-mail digitado já está sendo utilizado por outro usuário.");
        }

        return '';
    }

    public function getEditUser($request, $id)
    {
        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            $request->getRouter()->redirect('/admin/users');
        }

        return parent::render('admin/pages/users/form.twig', [
            'title' => 'Editar usuário',
            'nome' => $userDTO->nome,
            'email' => $userDTO->email,
            'status' => self::getStatus($request)
        ], 'users');
    }

    public function setEditUser($request, $id)
    {
        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            $request->getRouter()->redirect('/admin/users');
        }

        $postVars = $request->getPostVars();

        $email = $postVars['email'] ?? '';
        $userByEmail = $this->userService->getUserByEmail($email);

        if ($userByEmail instanceof UserDTO && $userByEmail->id != $id) {
            $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=duplicated');
        }

        // Atualiza DTO
        $userDTO->nome = $postVars['nome'] ?? $userDTO->nome;
        $userDTO->email = $postVars['email'] ?? $userDTO->email;
        if (!empty($postVars['senha'])) {
            $userDTO->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        }

        $this->userService->updateUser($userDTO);

        $request->getRouter()->redirect('/admin/users/' . $userDTO->id . '/edit?status=updated');
    }

    public function getDeleteUser($request, $id)
    {
        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            $request->getRouter()->redirect('/admin/users');
        }

        return parent::render('admin/pages/users/delete.twig', [
            'nome' => $userDTO->nome,
            'email' => $userDTO->email,
        ], 'users');
    }

    public function setDeleteUser($request, $id)
    {
        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            $request->getRouter()->redirect('/admin/users');
        }

        $this->userService->deleteUser($id);

        $request->getRouter()->redirect('/admin/users/?status=deleted');
    }
}
