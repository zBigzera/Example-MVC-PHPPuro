<?php

namespace App\Model\Entity;

use App\Core\Container;

class Testimony
{
    protected $table = 'depoimentos';
    protected $database;

    public $id;
    public $nome;
    public $mensagem;
    public $data;

    public function __construct()
    {
        $factory = Container::resolve('database.factory');
        $this->database = $factory->create($this->table);
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

    public static function getTestimonyById($id)
    {
        $instance = new self();
        $stmt = $instance->database->select("id = {$id}", null, '1');
        $data = $stmt->fetch();
        if ($data) {
            return self::hydrate($data);
        }
        return false;
    }

    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
    {
        $instance = new self();
        $stmt = $instance->database->select($where, $order, $limit, $fields);
        return $stmt->fetchAll();
    }

    public static function hydrate(array $data)
    {
        $obj = new self();
        $obj->id = $data['id'] ?? null;
        $obj->nome = $data['nome'] ?? null;
        $obj->mensagem = $data['mensagem'] ?? null;
        $obj->data = $data['data'] ?? null;
        return $obj;
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
