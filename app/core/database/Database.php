<?php

namespace App\Core\Database;

use \PDO;
use \PDOStatement;
use \PDOException;

class Database
{
    /**
     * Instância de conexão com o banco de dados
     * @var PDO
     */
    private $connection;

    /**
     * Nome da tabela a ser manipulada
     * @var string
     */
    private $table;

    /**
     * Última query SQL gerada
     * @var string
     */
    private $lastQuery = "";

    /**
     * Últimos parâmetros vinculados à query
     * @var array
     */
    private $lastParams = [];

    public function __construct(PDO $connection, $table = null)
    {
        if ($table !== null) {
            $this->setTable($table);
        }
        $this->connection = $connection;
    }

    /**
     * Método responsável por executar queries dentro do banco de dados
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
   public function execute($query, $params = [])
    {
        // Armazena a última query e parâmetros para depuração
        $this->lastQuery = $query;
        $this->lastParams = $params;

        $normalizedParams = [];
        foreach ($params as $key => $value) {
            $normalizedKey = str_starts_with($key, ":") ? $key : ":" . $key;
            $normalizedParams[$normalizedKey] = $value;
        }

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($normalizedParams);
            return $statement;
        } catch (PDOException $e) {
            die("ERROR: " . $e->getMessage());
        }
    }

    /**
     * Método responsável por inserir dados no banco
     * @param array $values [ field => value ]
     * @return integer ID inserido
     */
     public function insert($values)
    {
        //DADOS DA QUERY
        $fields = array_keys($values);
        // Usa placeholders nomeados para inserção
        $namedPlaceholders = implode(" , ", array_map(fn($field) => ":$field", $fields));

        //MONTA A QUERY
        $query = "INSERT INTO " . $this->table . " (" . implode(" , ", $fields) . ") VALUES (" . $namedPlaceholders . ")";

        //EXECUTA O INSERT com array associativo
        $this->execute($query, $values);

        //RETORNA O ID INSERIDO
        return $this->connection->lastInsertId();
    }

    /**
     * Método responsável por executar uma consulta no banco
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @param array $params
     * @return PDOStatement
     */
    public function select($where = null, $order = null, $limit = null, $fields = "*", $params = [])
    {
        $whereClause = !empty($where) ? "WHERE " . $where : "";
        $orderClause = !empty($order) ? "ORDER BY " . $order : "";
        $limitClause = !empty($limit) ? "LIMIT " . $limit : "";

        $query = "SELECT " . $fields . " FROM " . $this->table . " " . $whereClause . " " . $orderClause . " " . $limitClause;

        return $this->execute($query, $params);
    }

    /**
     * Método responsável por executar atualizações no banco de dados
     * @param string $where
     * @param array $values [ field => value ]
     * @param array $params
     * @return boolean
     */
    public function update($where, $values, $params = [])
    {
        // Extrai os campos que serão atualizados
        $fields = array_keys($values);

        // Monta a cláusula SET com placeholders nomeados, evitando conflito com parâmetros do WHERE
        $setClause = implode(" , ", array_map(fn($field) => "$field = :set_$field", $fields));

        // Monta a query final
        $query = "UPDATE " . $this->table . " SET " . $setClause . " WHERE " . $where;

        // Renomeia os parâmetros de SET com prefixo "set_" para evitar conflitos com o WHERE
        $setParams = [];
        foreach ($values as $k => $v) {
            $setParams["set_$k"] = $v;
        }

        // Executa a query com a junção dos parâmetros de SET e WHERE
        $this->execute($query, array_merge($setParams, $params));

        // Retorna sucesso
        return true;
    }

     /**
     * Método responsável por excluir dados do banco
     * @param string $where
     * @param array $params
     * @return boolean
     */
    public function delete($where, $params = [])
    {
        //MONTA A QUERY
        $query = "DELETE FROM " . $this->table . " WHERE " . $where;

        //EXECUTA A QUERY
        $this->execute($query, $params);

        //RETORNA SUCESSO
        return true;
    }

    /**
     * Retorna a instância da conexão PDO
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Define a tabela a ser manipulada
     * @param string $table
     */
   public function setTable(string $table)
    {
        // Proteção contra table injection
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $table)) {
            throw new \InvalidArgumentException("Invalid table name: $table");
        }
        $this->table = $table;
    }

    /**
     * Inicia uma transação
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commita uma transação
     * @return bool
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Reverte uma transação
     * @return bool
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Verifica se uma transação está ativa
     * @return bool
     */
    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

     /**
     * Retorna a última query SQL gerada e seus parâmetros.
     * Útil para depuração.
     * @return array ["query" => string, "params" => array]
     */
    public function getLastQuery(): array
    {
        return [
            "query" => $this->lastQuery,
            "params" => $this->lastParams
        ];
    }

}


