<?php

namespace App\Controller\Api;

use WilliamCosta\DatabaseManager\Pagination;

class Api{
    /**
     * Método responsável por retornar os detalhes da API
     * @param \App\Core\Http\Request $request
     * @return array
     */
    public static function getDetails($request){
        return [
            'nome' => 'API - BIG',
            'versao' => 'v1.0.0',
            'autor' => 'Otávio Bigogno',
            'email' => 'otaviobigogno37@gmail.com'
        ];
    }

    /**
     * Método responsável por retornar os detalhes da paginação
     * @param \App\Core\Http\Request $request
     * @param Pagination $obPagination
     * @return array
     */
    protected static function getPagination($request, $obPagination){
        $queryParams = $request->getQueryParams();

        $pages = $obPagination->getPages();

        return [
            'currentPage' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'totalPages ' => !empty($pages) ? count($pages) : 1
        ];
    }
}