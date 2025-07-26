<?php

namespace App\Model\Entity;

use App\Core\Database\Database;

class User
{
    protected $table = 'usuarios';
    protected $database;

    public $id;
    public $nome;
    public $email;
    public $senha;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->database->setTable($this->table);
    }

    public function cadastrar()
    {
        $this->id = $this->database->insert([
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
        return true;
    }

    public function atualizar()
    {
        return $this->database->update("id = {$this->id}", [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    public function excluir()
    {
        return $this->database->delete("id = {$this->id}");
    }

    public  function getUserById($id)
    {

        $stmt = $this->database->select("id = {$id}", null, '1');
        $data = $stmt->fetch();
        return $data ? $this->hydrate($data) : false;
    }

    public  function getUserByEmail($email)
    {
        $stmt = $this->database->select('email = "' . $email . '"', null, '1');
        $data = $stmt->fetch();
        return $data ? $this->hydrate($data) : false;
    }

    public  function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        $stmt = $this->database->select($where, $order, $limit, $fields);
        return $stmt->fetchAll();
    }

    public function count($where = null)
    {
        $stmt = $this->database->select($where, null, null, 'COUNT(*) as total');
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    public function hydrate(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->nome = $data['nome'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->senha = $data['senha'] ?? null;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ];
    }

    public function validate()
    {
        $errors = [];

        if (empty($this->nome)) {
            $errors[] = 'Nome é obrigatório';
        }

        if (empty($this->email)) {
            $errors[] = 'Email é obrigatório';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if (empty($this->senha)) {
            $errors[] = 'Senha é obrigatória';
        }

        return $errors;
    }
}
