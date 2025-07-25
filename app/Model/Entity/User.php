<?php

namespace App\Model\Entity;

use App\Core\Container;

class User
{
    protected $table = 'usuarios';
    protected $database;

    public $id;
    public $nome;
    public $email;
    public $senha;

    public function __construct()
    {
        $factory = Container::resolve('database.factory');
        $this->database = $factory->create($this->table);
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

    public static function getUserById($id)
    {
        $instance = new self();
        $stmt = $instance->database->select("id = {$id}", null, '1');
        $data = $stmt->fetch();
        return $data ? self::hydrate($data) : false;
    }

    public static function getUserByEmail($email)
    {
        $instance = new self();
        $stmt = $instance->database->select('email = "' . $email . '"', null, '1');
        $data = $stmt->fetch();
        return $data ? self::hydrate($data) : false;
    }

    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        $instance = new self();
        $stmt = $instance->database->select($where, $order, $limit, $fields);
        return $stmt->fetchAll();
    }

    public function count($where = null)
    {
        $stmt = $this->database->select($where, null, null, 'COUNT(*) as total');
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    public static function hydrate(array $data)
    {
        $user = new self();
        $user->id = $data['id'] ?? null;
        $user->nome = $data['nome'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->senha = $data['senha'] ?? null;
        return $user;
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
