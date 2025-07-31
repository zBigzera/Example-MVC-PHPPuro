<?php

namespace App\Model\DAO;

use App\Model\DTO\UserDTO;


class UserDAO extends AbstractDAO
{
    protected string $table = 'usuarios';

    public function getUserById(int $id) : ?UserDto {
        try{

            $query = $this->database->select($this->table, "id = :id", null, null, "*", [":id" => $id])->fetch(\PDO::FETCH_ASSOC);

            if (!$query) {
                return null;
            }

            return UserDTO::fromArray($query);
        }
        catch (\PDOException $e) {

            throw new \Exception("Erro ao buscar usuário: " . $e->getMessage());

        }
    }

    public function getUserByEmail(string $email): ?UserDTO
    {
        try {
            $query = $this->database->select($this->table, 'email = :email', null, null, '*', [':email' => $email])->fetch(\PDO::FETCH_ASSOC);


            if (!$query) {
                return null;
            }

            return UserDTO::fromArray($query);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar usuário por email: " . $e->getMessage());
        }
    }

    public function getUsers(?string $where = null, ?string $order = null, ?string $limit = null, array $params = []): array
    {
        try {
            $statement = $this->database->select($this->table, $where, $order, $limit, "*", $params);

            $users = [];

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $users[] = UserDTO::fromArray($row);
            }

            return $users;
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar usuários: " . $e->getMessage());
        }
    }

    public function cadastrar(UserDTO $dados) : int {
        try {
            $data = $dados->toArray();
            unset($data['id']);

            return $this->database->insert($this->table, $data);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao cadastrar usuário: " . $e->getMessage());
        }
    }

    public function atualizar(UserDTO $dados): bool
    {
        try {
            
            $data = [
                'nome' => $dados->nome,
                'email' => $dados->email,
                'senha' => $dados->senha
            ];

            $update = $this->database->update($this->table, 'id = :id', $data, ['id' => $dados->id]);

            return $update;

        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }

      public function excluir(int $id): int
    {
        try {
            return $this->database->delete($this->table, "id = :id", ['id' => $id]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao excluir depoimento: " . $e->getMessage());
        }
    }

    

     public function count($where = null)
    {
        $statement = $this->database->select($this->table, $where, null, null, 'COUNT(*) as total');
        $result = $statement->fetch();
        return (int) $result['total'];
    }

}