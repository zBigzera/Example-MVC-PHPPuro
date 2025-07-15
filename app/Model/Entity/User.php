<?php

namespace App\Model\Entity;

use App\Core\Model;

class User extends Model
{
    /**
     * Nome da tabela
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * ID do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * Email do usuário
     * @var string
     */
    public $email;

    /**
     * Senha do usuário
     * @var string
     */
    public $senha;

    /**
     * Método responsável por retornar um usuário com base em seu e-mail
     * @param string $email
     * @return User|false
     */
    public static function getUserByEmail($email)
    {
        $instance = new self();
        $userData = $instance->findOne('email = "' . $email . '"');
        
        if ($userData) {
            return self::hydrate($userData);
        }
        
        return false;
    }

    /**
     * Método responsável por retornar usuários
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        $instance = new self();
        return $instance->findAll($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por cadastrar o usuário
     * @return bool
     */
    public function cadastrar()
    {
        $this->id = $this->create([
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        return true;
    }

    /**
     * Método responsável por atualizar o usuário
     * @return bool
     */
    public function atualizar()
    {
        return $this->updateById($this->id, [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    /**
     * Método responsável por excluir o usuário
     * @return bool
     */
    public function excluir()
    {
        return $this->deleteById($this->id);
    }

    /**
     * Método responsável por retornar um usuário por ID
     * @param int $id
     * @return User|false
     */
    public static function getUserById($id)
    {
        $instance = new self();
        $userData = $instance->findById($id);
        
        if ($userData) {
            return self::hydrate($userData);
        }
        
        return false;
    }

    /**
     * Método para hidratar uma instância da classe com dados do banco
     * @param array $data
     * @return User
     */
    public static function hydrate(array $data)
    {
        $user = new self();
        $user->id = $data['id'] ?? null;
        $user->nome = $data['nome'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->senha = $data['senha'] ?? null;
        
        return $user;
    }

    /**
     * Método para converter a instância em array
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ];
    }

    /**
     * Método para validar os dados do usuário
     * @return array Erros de validação
     */
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