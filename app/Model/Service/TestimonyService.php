<?php

namespace App\Model\Service;

use App\Model\Dao\TestimonyDAO;
use App\Model\Dto\TestimonyDTO;
use App\Core\Database\Pagination;

class TestimonyService
{

    private TestimonyDAO $testimonyDAO;

    public function __construct(TestimonyDAO $testimonyDAO)
    {
        $this->testimonyDAO = $testimonyDAO;
    }

    public function createTestimony(TestimonyDTO $testimonyDTO): int
    {   
        if (!$testimonyDTO->nome || !$testimonyDTO->mensagem) {
            throw new \InvalidArgumentException("Os campos 'nome' e 'mensagem' precisam ser informados.");
        }

        return $this->testimonyDAO->cadastrar($testimonyDTO);
    }

    public function updateTestimony(TestimonyDTO $testimonyDTO): bool
    {
        if(empty($testimonyDTO->id)){
            throw new \InvalidArgumentException("Um ID precisa ser informado.");
        }
        return $this->testimonyDAO->atualizar($testimonyDTO);
    }

    public function deleteTestimony(int $id): int
    {
        return $this->testimonyDAO->excluir($id);
    }

    public function getTestimonyById(int $id): ?TestimonyDTO
    {
        return $this->testimonyDAO->getTestimonyById($id);
    }

    public function getTestimonies(?string $where = null, ?string $order = 'data DESC', int $page = 1, int $itemsPerPage = 10 , $params = []): array
{
    $totalItems = $this->testimonyDAO->count($where);

    $pagination = new Pagination($page, $itemsPerPage, $totalItems);

    $testimonies = $this->testimonyDAO->getTestimonies($where, $order, $pagination->getLimit(), $params);

    return [
        'data' => $testimonies,
        'pagination' => $pagination
    ];
}

}

