<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
class User
{
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
     * @return User
     */
    public static function getUserByEmail($email){
        return (new Database('usuarios'))->select('email = "'.$email.'"')->fetchObject(self::class);
    }

       /**
     * Método responsável por retornar usuarios
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     */
     public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
  {
    return (new Database('usuarios'))->select($where, $order, $limit, $fields);
  }

  public function cadastrar(){
        $this->id = (new Database('usuarios'))->insert(
            [
                'nome' => $this->nome,
                'email' => $this->email,
                'senha' => $this->senha
            ]
            );

            return true;
    }

      public function atualizar(){
        return (new Database('usuarios'))->update('id = '. $this->id,
            [
                'nome' => $this->nome,
                'email' => $this->email,
                'senha' => $this->senha
            ]
            );
    }
    
    public function excluir(){
        return (new Database('usuarios'))->delete('id = '. $this->id);
    }

     public static function getUserById($id){
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }

}