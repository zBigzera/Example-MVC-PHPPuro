<?php

namespace App\Model\Entity;

use App\Core\Model;

class Testimony extends Model
{
    /**
     * Nome da tabela
     * @var string
     */
    protected $table = 'depoimentos';

    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do autor do depoimento
     * @var string
     */
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data do depoimento
     * @var string
     */
    public $data;

    /**
     * Cria uma instância a partir de um array
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $obj = new self();
        $obj->id = $data['id'] ?? null;
        $obj->nome = $data['nome'] ?? null;
        $obj->mensagem = $data['mensagem'] ?? null;
        $obj->data = $data['data'] ?? null;
        return $obj;
    }

    /**
     * Método responsável por cadastrar o depoimento
     * @return bool
     */
    public function cadastrar()
    {
        $this->data = date('Y-m-d H:i:s');
        $this->id = $this->create([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

        return true;
    }

    /**
     * Método responsável por atualizar o depoimento
     * @return bool
     */
    public function atualizar()
    {
        return $this->updateById($this->id, [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    /**
     * Método responsável por excluir o depoimento
     * @return bool
     */
    public function excluir()
    {
        return $this->deleteById($this->id);
    }

    /**
     * Método responsável por retornar um depoimento por ID
     * @param int $id
     * @return Testimony|false
     */
    public static function getTestimonyById($id)
    {
        $instance = new self();
        $testimonyData = $instance->findById($id);
        
        if ($testimonyData) {
            return self::hydrate($testimonyData);
        }
        
        return false;
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return array
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*')
    {
        $instance = new self();
        return $instance->findAll($where, $order, $limit, $fields);
    }

    /**
     * Método para hidratar uma instância da classe com dados do banco
     * @param array $data
     * @return Testimony
     */
    public static function hydrate(array $data)
    {
        $testimony = new self();
        $testimony->id = $data['id'] ?? null;
        $testimony->nome = $data['nome'] ?? null;
        $testimony->mensagem = $data['mensagem'] ?? null;
        $testimony->data = $data['data'] ?? null;
        
        return $testimony;
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
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ];
    }

    /**
     * Método para validar os dados do depoimento
     * @return array Erros de validação
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->nome)) {
            $errors[] = 'Nome é obrigatório';
        }

        if (empty($this->mensagem)) {
            $errors[] = 'Mensagem é obrigatória';
        }

        return $errors;
    }

    /**
     * Método para buscar depoimentos com paginação
     * @param int $page
     * @param int $itemsPerPage
     * @param string $where
     * @param string $order
     * @return array
     */
    public static function getTestimoniesWithPagination($page = 1, $itemsPerPage = 10, $where = null, $order = 'data DESC')
    {
        $instance = new self();
        
        // Conta o total de registros
        $totalItems = $instance->count($where);
        
        // Cria a paginação
        $pagination = new \App\Core\Database\Pagination($page, $itemsPerPage, $totalItems);
        
        // Busca os dados com limite
        $testimonies = $instance->findAll($where, $order, $pagination->getLimit());
        
        return [
            'data' => $testimonies,
            'pagination' => $pagination->toArray()
        ];
    }
}

