<?php

namespace App\Controller\Api;

use App\Model\Service\TestimonyService as Service;
use App\Model\Dto\TestimonyDTO as Dto;
use App\Core\Database\Pagination;
use App\Core\Http\Request;

class Testimony extends Api
{
    private $testimonyService;

    public function __construct(Service $service)
    {
        $this->testimonyService = $service;
    }

    public function getTestimonies($request)
    {
        return [
            'depoimentos' => $this->getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    public function getTestimony($request, $id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }

        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        return [
            'id' => (int)$dto->id,
            'nome' => $dto->nome,
            'mensagem' => $dto->mensagem,
            'data' => $dto->data
        ];
    }

    private function getTestimonyItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $result = $this->testimonyService->getTestimonies(null, "id DESC", $paginaAtual, 5);

        $obPagination = $result['pagination'];

        $itens = [];
        foreach ($result['data'] as $dto) {
            $itens[] = [
                'id' => (int)$dto->id,
                'nome' => $dto->nome,
                'mensagem' => $dto->mensagem,
                'data' => $dto->data
            ];
        }

        return $itens;
    }

    public function setNewTestimony($request)
    {
        $postVars = $request->getPostVars();

        if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        $dto = Dto::fromArray([
            'id' => null,
            'nome' => $postVars['nome'],
            'mensagem' => $postVars['mensagem'],
            'data' => date('Y-m-d H:i:s')
        ]);

        $this->testimonyService->createTestimony($dto);

        return [
            'id' => (int)$dto->id,
            'nome' => $dto->nome,
            'mensagem' => $dto->mensagem,
            'data' => $dto->data
        ];
    }

    public function setEditTestimony($request, $id)
    {
        $postVars = $request->getPostVars();

        if (!isset($postVars['nome']) || !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios", 400);
        }

        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        $dto->nome = $postVars['nome'];
        $dto->mensagem = $postVars['mensagem'];

        $this->testimonyService->updateTestimony($dto);

        return [
            'id' => (int)$dto->id,
            'nome' => $dto->nome,
            'mensagem' => $dto->mensagem,
            'data' => $dto->data
        ];
    }

    public function setDeleteTestimony($request, $id)
    {
        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        $this->testimonyService->deleteTestimony($id);

        return ['sucesso' => true];
    }
}
