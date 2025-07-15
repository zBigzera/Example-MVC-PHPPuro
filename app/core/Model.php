<?php

namespace App\Core;

use App\Core\Container;
use App\Core\Database\Database;
use App\Core\Database\DatabaseFactory;

abstract class Model
{
    /**
     * Nome da tabela
     * @var string
     */
    protected $table;

    /**
     * Instância do Database
     * @var Database
     */
    protected $database;

    /**
     * Construtor da classe Model
     */
    public function __construct()
    {
        if (empty($this->table)) {
            throw new \Exception('Table name must be defined in model');
        }

        // Resolve a DatabaseFactory do container
        $factory = Container::resolve('database.factory');
        
        // Cria uma instância de Database para esta tabela
        $this->database = $factory->create($this->table);
    }

    /**
     * Método para inserir dados
     * @param array $data
     * @return int
     */
    public function create(array $data)
    {
        return $this->database->insert($data);
    }

    /**
     * Método para buscar todos os registros
     * @param string|null $where
     * @param string|null $order
     * @param string|null $limit
     * @param string $fields
     * @return array
     */
    public function findAll($where = null, $order = null, $limit = null, $fields = '*')
    {
        $statement = $this->database->select($where, $order, $limit, $fields);
        return $statement->fetchAll();
    }

    /**
     * Método para buscar um registro
     * @param string $where
     * @param string $fields
     * @return array|false
     */
    public function findOne($where, $fields = '*')
    {
        $statement = $this->database->select($where, null, '1', $fields);
        return $statement->fetch();
    }

    /**
     * Método para buscar por ID
     * @param int $id
     * @param string $fields
     * @return array|false
     */
    public function findById($id, $fields = '*')
    {
        return $this->findOne("id = {$id}", $fields);
    }

    /**
     * Método para atualizar dados
     * @param string $where
     * @param array $data
     * @return bool
     */
    public function update($where, array $data)
    {
        return $this->database->update($where, $data);
    }

    /**
     * Método para atualizar por ID
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateById($id, array $data)
    {
        return $this->update("id = {$id}", $data);
    }

    /**
     * Método para deletar dados
     * @param string $where
     * @return bool
     */
    public function delete($where)
    {
        return $this->database->delete($where);
    }

    /**
     * Método para deletar por ID
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        return $this->delete("id = {$id}");
    }

    /**
     * Método para contar registros
     * @param string|null $where
     * @return int
     */
    public function count($where = null)
    {
        $statement = $this->database->select($where, null, null, 'COUNT(*) as total');
        $result = $statement->fetch();
        return (int) $result['total'];
    }

    /**
     * Método para executar query customizada
     * @param string $query
     * @param array $params
     * @return \PDOStatement
     */
    public function query($query, $params = [])
    {
        return $this->database->execute($query, $params);
    }

    /**
     * Retorna a instância do Database
     * @return Database
     */
    protected function getDatabase()
    {
        return $this->database;
    }

    /**
     * Retorna o nome da tabela
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}

