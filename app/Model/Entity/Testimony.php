<?php

namespace App\Model\Entity;

use App\Core\Database\Database;

class Testimony
{
    protected $table = 'depoimentos';
    protected $database;

    public $id;
    public $nome;
    public $mensagem;
    public $data;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->database->setTable($this->table);
    }

    public function cadastrar()
    {
        $this->data = date('Y-m-d H:i:s');
        $this->id = $this->database->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);
        return true;
    }

    public function atualizar()
    {
        return $this->database->update("id = {$this->id}", [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    public function excluir()
    {
        return $this->database->delete("id = {$this->id}");
    }

    public function getTestimonyById($id)
    {
        
        $stmt = $this->database->select("id = :id", null, 1, "*", [':id'=> $id]);
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return self::hydrate($data);
        }
        return false;
    }

    public  function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
    {
        
        $stmt = $this->database->select($where, $order, $limit, $fields);
        return $stmt->fetchAll();
    }

    public function hydrate(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->nome = $data['nome'] ?? null;
        $this->mensagem = $data['mensagem'] ?? null;
        $this->data = $data['data'] ?? null;
        return $this;
    }

    public function count($where = null)
    {
        $stmt = $this->database->select($where, null, null, 'COUNT(*) as total');
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data,
        ];
    }

    public function validate()
    {
        $errors = [];
        if (empty($this->nome)) $errors[] = 'Nome é obrigatório';
        if (empty($this->mensagem)) $errors[] = 'Mensagem é obrigatória';
        return $errors;
    }
}
