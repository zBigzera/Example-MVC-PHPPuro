<?php

namespace App\Controller\Site;

use App\Core\View;
use App\Model\Service\TestimonyService as Service;
use App\Model\Dto\TestimonyDTO as Dto;

class Testimony
{

    private $testimonyService;

    public function __construct(Service $service)
    {
        $this->testimonyService = $service;
    }

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @return array
     */
    private function getTestimonyItems($request, &$obPagination)
    {
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams["page"] ?? 1;

        $result = $this->testimonyService->getTestimonies(null, "id DESC", $paginaAtual, 2);

        $obPagination = $result['pagination'];

        $itens = [];

        foreach ($result['data'] as $dto) {
            $itens[] = [
                "nome" => $dto->nome,
                "mensagem" => $dto->mensagem,
                "data" => date("d/m/Y H:i:s", strtotime($dto->data))
            ];
        }

        return $itens;
    }


    /**
     * Método responsável por retornar o conteúdo (view)
     * @return string
     */
    public function getTestimonies($request)
    {

        return View::render("site/pages/testimonies/index.twig", [
            'title' => 'Depoimentos',
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => $obPagination->getPagination($request->getFullUrl(), 'page')
        ]);
    }

    public function insertTestimony($request)
    {

        $postVars = $request->getPostVars();

        $testimonyDTO = Dto::fromArray([
        'id' => null,
        'nome' => $postVars['nome'] ?? '',
        'mensagem' => $postVars['mensagem'] ?? '',
        'data' => date('Y-m-d H:i:s')
    ]);

        try {
            $this->testimonyService->createTestimony($testimonyDTO);
            // Redirecionar para uma página de sucesso ou exibir mensagem
            return $request->getRouter()->redirect("/depoimentos?status=success");
        } catch (\InvalidArgumentException $e) {
            // Lidar com erros de validação
            return $request->getRouter()->redirect("/depoimentos/?errors=" . urlencode($e->getMessage()));
        } catch (\Exception $e) {
            // Lidar com outros erros (ex: erro de banco de dados)
            return $request->getRouter()->redirect("/depoimentos/?errors=" . urlencode("Ocorreu um erro inesperado: " . $e->getMessage()));
        }
    }
}