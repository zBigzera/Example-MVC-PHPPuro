<?php

namespace App\Core\Database;

class Pagination
{
    /**
     * Número da página atual
     * @var int
     */
    private $currentPage;

    /**
     * Quantidade de itens por página
     * @var int
     */
    private $itemsPerPage;

    /**
     * Total de itens
     * @var int
     */
    private $totalItems;

    /**
     * Total de páginas
     * @var int
     */
    private $totalPages;

    /**
     * Construtor da paginação
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $totalItems
     */
    public function __construct($currentPage = 1, $itemsPerPage = 10, $totalItems = 0)
    {
        $this->currentPage = max(1, (int) $currentPage);
        $this->itemsPerPage = max(1, (int) $itemsPerPage);
        $this->totalItems = max(0, (int) $totalItems);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);

        // Ajusta a página atual se for maior que o total de páginas
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }

    /**
     * Retorna o LIMIT para a query SQL
     * @return string
     */
    public function getLimit()
    {
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;
        return $offset . ',' . $this->itemsPerPage;
    }

    /**
     * Retorna o offset (início) para a query
     * @return int
     */
    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    /**
     * Retorna a página atual
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Retorna a quantidade de itens por página
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Retorna o total de itens
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * Retorna o total de páginas
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * Verifica se existe página anterior
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    /**
     * Verifica se existe próxima página
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Retorna o número da página anterior
     * @return int|null
     */
    public function getPreviousPage()
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }

    /**
     * Retorna o número da próxima página
     * @return int|null
     */
    public function getNextPage()
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    /**
     * Retorna um array com os números das páginas para navegação
     * @param int $range Quantidade de páginas para mostrar de cada lado da atual
     * @return array
     */

    /**
     * Retorna informações completas da paginação
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage,
            'items_per_page' => $this->itemsPerPage,
            'total_items' => $this->totalItems,
            'total_pages' => $this->totalPages,
            'has_previous' => $this->hasPreviousPage(),
            'has_next' => $this->hasNextPage(),
            'previous_page' => $this->getPreviousPage(),
            'next_page' => $this->getNextPage(),
            'offset' => $this->getOffset(),
            'limit' => $this->getLimit()
        ];
    }


    /**
     * Constrói a URL com o parâmetro da página
     * @param string $baseUrl
     * @param string $pageParam
     * @param int $page
     * @return string
     */
    private function buildUrl(string $baseUrl, string $pageParam, int|string $page): string
    {
        // Parseia a URL e seus componentes
        $urlParts = parse_url($baseUrl);

        // Extrai query params atuais
        $queryParams = [];
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $queryParams);
        }

        // Atualiza o parâmetro da página
        $queryParams[$pageParam] = $page;

        // Reconstrói a query string
        $newQuery = http_build_query($queryParams);

        // Reconstrói a URL com a query atualizada
        $finalUrl = $urlParts['path'] ?? '';
        if ($newQuery) {
            $finalUrl .= '?' . $newQuery;
        }

        return $finalUrl;
    }


    /**
     * Retorna um array com os números das páginas para navegação otimizada.
     * Exibe sempre a primeira e a última página, links de anterior/próximo,
     * e um intervalo de páginas ao redor da página atual, com '...' para lacunas.
     * @param int $range Quantidade de páginas para mostrar de cada lado da atual
     * @return array
     */
    private function getPageRange(int $maxVisible): array
    {
        if ($this->totalPages <= $maxVisible) {
            return range(1, $this->totalPages);
        }

        $pages = [1];
        $numSlots = $maxVisible - 2;

        $start = max(2, $this->currentPage - floor($numSlots / 2));
        $end = min($this->totalPages - 1, $start + $numSlots - 1);

        if ($end - $start + 1 < $numSlots) {
            $start = max(2, $end - $numSlots + 1);
        }

        if ($start > 2) {
            $pages[] = '...';
        }

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($end < $this->totalPages - 1) {
            $pages[] = '...';
        }

        $pages[] = $this->totalPages;

        return $pages;
    }


    public function getPagination(string $baseUrl, string $pageParam = 'page', int $maxVisible = 7): array
    {
        if ($this->totalPages <= 1) {
            return [
                [
                    'page' => 1,
                    'link' => null,
                    'active' => true
                ]
            ];
        }

        $pagination = [];

        // Botão << (Anterior)
        if ($this->hasPreviousPage()) {
            $pagination[] = [
                'page' => '<<',
                'link' => $this->buildUrl($baseUrl, $pageParam, $this->getPreviousPage()),
                'active' => false
            ];
        }

        foreach ($this->getPageRange($maxVisible) as $page) {
            if ($page === '...') {
                $pagination[] = [
                    'page' => '...',
                    'link' => null,
                    'disabled' => true
                ];
            } else {
                $pagination[] = [
                    'page' => $page,
                    'link' => $this->buildUrl($baseUrl, $pageParam, $page),
                    'active' => $page == $this->currentPage
                ];
            }
        }

        // Botão >> (Próxima)
        if ($this->hasNextPage()) {
            $pagination[] = [
                'page' => '>>',
                'link' => $this->buildUrl($baseUrl, $pageParam, $this->getNextPage()),
                'active' => false
            ];
        }

        return $pagination;
    }




}