<?php

namespace App\Model\DAO;

use App\Model\DTO\TestimonyDTO;

class TestimonyDAO extends AbstractDAO
{
    protected string $table = 'depoimentos';

    public function getTestimonyByID(int $id): ?TestimonyDTO
    {
        try{

            $query = $this->database->select($this->table, "id = :id", null, null, "*", [":id" => $id])->fetch(\PDO::FETCH_ASSOC);

            if (!$query) {
                return null;
            }

            return TestimonyDTO::fromArray($query);
        }catch (\PDOException $e) {

            throw new \Exception("Erro ao buscar depoimento: " . $e->getMessage());

        }

    }

      public function getTestimonies(?string $where = null, ?string $order = null, ?string $limit = null, array $params = []): array
    {
        try {
            $statement = $this->database->select($this->table, $where, $order, $limit, "*", $params);

            $testimonies = [];

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $testimonies[] = TestimonyDTO::fromArray($row);
            }

            return $testimonies;
        } catch (\PDOException $e) {
            // Log the error: error_log("Database error: " . $e->getMessage());
            throw new \Exception("Erro ao buscar depoimentos: " . $e->getMessage());
        }
    }

    public function cadastrar(TestimonyDTO $dados): int
    {
        try {
            $data = $dados->toArray();
            unset($data['id']);

            return $this->database->insert($this->table, $data);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao cadastrar depoimento: " . $e->getMessage());
        }
    }

    public function atualizar(TestimonyDTO $dados): bool
    {

        try {
            $data = [
                'nome' => $dados->nome,
                'mensagem' => $dados->mensagem
            ];

            return $this->database->update($this->table, 'id = :id', $data, ['id' => $dados->id]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar depoimento: " . $e->getMessage());
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