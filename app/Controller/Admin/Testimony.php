<?php

namespace App\Controller\Admin;

use App\Core\Http\Request;
use App\Core\View;
use App\Model\Service\TestimonyService as Service;
use App\Model\Dto\TestimonyDTO as Dto;


class Testimony extends Page
{
    private $testimonyService;

    public function __construct(Service $service)
    {
        $this->testimonyService = $service;
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
                "id" => $dto->id,
                "nome" => $dto->nome,
                "mensagem" => $dto->mensagem,
                "data" => date("d/m/Y H:i:s", strtotime($dto->data))
            ];
        }

        return $itens;
    }

    public function getTestimonies($request)
    {
        return parent::render('admin/pages/testimonies/index.twig', [
            'title' => 'Depoimentos',
            'itens' => $this->getTestimonyItems($request, $obPagination),
            'pagination' => $obPagination->getPagination($request->getFullUrl(),'page'),
            'status' => self::getStatus($request)
        ], 'testimonies');
    }

    public function getNewTestimony($request)
    {
        return parent::render('admin/pages/testimonies/form.twig', [
            'title' => 'Cadastrar depoimento',
            'nome' => '',
            'mensagem' => '',
            'status' => ''
        ], 'testimonies');
    }

    public function setNewTestimony($request)
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
            return $request->getRouter()->redirect('/admin/testimonies?status=created');
        } catch (\InvalidArgumentException $e) {
            return $request->getRouter()->redirect('/admin/testimonies/new?errors=' . urlencode($e->getMessage()));
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/testimonies/new?errors=' . urlencode('Erro inesperado: ' . $e->getMessage()));
        }
    }

    private static function getStatus($request)
    {
        $queryParams = $request->getQueryParams();

        if(!isset($queryParams['status'])) return '';

        switch($queryParams['status']){
            case 'created': return Alert::getSuccess('Depoimento criado com sucesso!');
            case 'updated': return Alert::getSuccess('Depoimento editado com sucesso!');
            case 'deleted': return Alert::getSuccess('Depoimento deletado com sucesso!');
        }

        return '';
    }

    public function getEditTestimony($request, $id)
    {
        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            return $request->getRouter()->redirect('/admin/testimonies');
        }

        return parent::render('admin/pages/testimonies/form.twig', [
            'title' => 'Editar depoimento',
            'nome' => $dto->nome,
            'mensagem'=> $dto->mensagem,
            'status' => self::getStatus($request)
        ], 'testimonies');
    }

    public function setEditTestimony($request, $id)
    {
        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            return $request->getRouter()->redirect('/admin/testimonies');
        }

        $postVars = $request->getPostVars();

        $dto->nome = $postVars['nome'] ?? $dto->nome;
        $dto->mensagem = $postVars['mensagem'] ?? $dto->mensagem;

        try {
            $this->testimonyService->updateTestimony($dto);
            return $request->getRouter()->redirect("/admin/testimonies/{$id}/edit?status=updated");
        } catch (\Exception $e) {
            return $request->getRouter()->redirect("/admin/testimonies/{$id}/edit?errors=" . urlencode('Erro ao atualizar: ' . $e->getMessage()));
        }
    }

    public function getDeleteTestimony($request, $id)
    {
        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            return $request->getRouter()->redirect('/admin/testimonies');
        }

        return parent::render('admin/pages/testimonies/delete.twig', [
            'nome' => $dto->nome,
            'mensagem'=> $dto->mensagem,
        ], 'testimonies');
    }

    public function setDeleteTestimony($request, $id)
    {
        $dto = $this->testimonyService->getTestimonyById($id);

        if (!$dto instanceof Dto) {
            return $request->getRouter()->redirect('/admin/testimonies');
        }

        try {
            $this->testimonyService->deleteTestimony($id);
            return $request->getRouter()->redirect('/admin/testimonies?status=deleted');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/testimonies?errors=' . urlencode('Erro ao deletar: ' . $e->getMessage()));
        }
    }
}
