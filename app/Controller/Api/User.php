<?php

namespace App\Controller\Api;

use App\Core\Database\Pagination;
use App\Core\Http\Request;
use App\Model\Dto\UserDTO;
use App\Model\Service\UserService;

class User extends Api
{
    private $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function getUsers($request)
    {
        return [
            'usuarios' => $this->getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    public function getCurrentUser($request)
    {
        $obUser = $request->user;

        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    public function getUser($request, $id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }

        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }

        return [
            'id' => (int)$userDTO->id,
            'nome' => $userDTO->nome,
            'email' => $userDTO->email
        ];
    }

    private function getUserItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $result = $this->userService->getUsers(null, "id DESC", $paginaAtual, 5);

        $obPagination = $result['pagination'];
        $users = $result['data'];

        $itens = [];
        foreach ($users as $userDTO) {
            $itens[] = [
                'id' => (int)$userDTO->id,
                'nome' => $userDTO->nome,
                'email' => $userDTO->email
            ];
        }

        return $itens;
    }

    public function setNewUser($request)
    {
        $postVars = $request->getPostVars();

        if (!isset($postVars['nome'], $postVars['email'], $postVars['senha'])) {
            throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
        }

        if ($this->userService->getUserByEmail($postVars['email']) instanceof UserDTO) {
            throw new \Exception("O e-mail '".$postVars['email']."' já está em uso", 400);
        }

        $userDTO = UserDTO::fromArray([
            'id' => null,
            'nome' => $postVars['nome'],
            'email' => $postVars['email'],
            'senha' => password_hash($postVars['senha'], PASSWORD_DEFAULT)
        ]);

        $this->userService->createUser($userDTO);

        return [
            'id' => (int)$userDTO->id,
            'nome' => $userDTO->nome,
            'email' => $userDTO->email
        ];
    }

    public function setEditUser($request, $id)
    {
        //envia no body da requisição
        $postVars = $request->getPostVars();
     
        if (!isset($postVars['nome'], $postVars['email'])) {
            throw new \Exception("Os campos 'nome' e 'email' são obrigatórios", 400);
        }

        $userDTO = $this->userService->getUserById($id);
        if (!$userDTO instanceof UserDTO) {
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }

        $userByEmail = $this->userService->getUserByEmail($postVars['email']);
        if ($userByEmail instanceof UserDTO && $userByEmail->id != $userDTO->id) {
            throw new \Exception("O e-mail '".$postVars['email']."' já está em uso", 400);
        }

        $userDTO->nome = $postVars['nome'];
        $userDTO->email = $postVars['email'];

        if (!empty($postVars['senha'])) {
            $userDTO->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        }

        $this->userService->updateUser($userDTO);

        return [
            'id' => (int)$userDTO->id,
            'nome' => $userDTO->nome,
            'email' => $userDTO->email
        ];
    }

    public function setDeleteUser($request, $id)
    {
        $userDTO = $this->userService->getUserById($id);

        if (!$userDTO instanceof UserDTO) {
            throw new \Exception("O usuário ".$id." não foi encontrado", 404);
        }

        $this->userService->deleteUser($id);

        return ['sucesso' => true];
    }
}
