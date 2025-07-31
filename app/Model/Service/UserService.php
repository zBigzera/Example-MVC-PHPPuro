<?php
namespace App\Model\Service;

use App\Model\DAO\UserDAO;
use App\Model\DTO\UserDTO;
use App\Core\Database\Pagination;

class UserService
{
    private UserDAO $userDAO;

    public function __construct(UserDAO $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    public function login(string $email, string $senha)
    {
        $user = $this->getUserByEmail($email);
   
        if (!$user instanceof UserDTO) {
            return false;
        }
        if (!password_verify($senha, $user->senha)) {
            return false;
        }
        return $user;
    }


    public function createUser(UserDTO $userDTO): int
    {
        if (empty($userDTO->nome) || empty($userDTO->email) || empty($userDTO->senha)) {
            throw new \InvalidArgumentException("Nome, email e senha são obrigatórios.");
        }
        return $this->userDAO->cadastrar($userDTO);
    }

    public function updateUser(UserDTO $userDTO): bool
    {
        if (empty($userDTO->id)) {
            throw new \InvalidArgumentException("Um ID precisa ser informado.");
        }
        return $this->userDAO->atualizar($userDTO);
    }

    public function getUsers(?string $where = null, ?string $order = 'data DESC', int $page = 1, int $itemsPerPage = 10, $params = []): array
    {
        $totalItems = $this->userDAO->count($where);

        $pagination = new Pagination($page, $itemsPerPage, $totalItems);

        $users = $this->userDAO->getUsers($where, $order, $pagination->getLimit(), $params);

        return [
            'data' => $users,
            'pagination' => $pagination
        ];
    }

    public function deleteUser(int $id): bool
    {
        return $this->userDAO->excluir($id);
    }

    public function getUserById(int $id): ?UserDTO
    {
        return $this->userDAO->getUserById($id);
    }

    public function getUserByEmail(string $email): ?UserDTO
    {
        return $this->userDAO->getUserByEmail($email);
    }
}