<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;
class Testimony{

    public $id;

    public $nome;

    public $mensagem;

    public $data;

    public static function fromArray(array $data): self {
        $obj = new self();
        $obj->id = $data['id'] ?? null;
        $obj->nome = $data['nome'] ?? null;
        $obj->mensagem = $data['mensagem'] ?? null;
        $obj->data = $data['data'] ?? null;
        return $obj;
    }
    
    public function cadastrar(){
        $this->data = date('Y-m-d h:i:s');
        $this->id = (new Database('depoimentos'))->insert(
            [
                'nome' => $this->nome,
                'mensagem' => $this->mensagem,
                'data' => $this->data
            ]
            );

            return true;
    }
     public function atualizar(){
        return (new Database('depoimentos'))->update('id = '. $this->id,
            [
                'nome' => $this->nome,
                'mensagem' => $this->mensagem
            ]
            );
    }
    
    public function excluir(){
        return (new Database('depoimentos'))->delete('id = '. $this->id);
    }


    public static function getTestimonyById($id){
        return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     */
     public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
  {
    return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
  }

}