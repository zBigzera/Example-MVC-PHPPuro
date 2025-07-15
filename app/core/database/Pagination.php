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
    public function getPageRange($range = 2)
    {
        $start = max(1, $this->currentPage - $range);
        $end = min($this->totalPages, $this->currentPage + $range);
        
        return range($start, $end);
    }

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
            'page_range' => $this->getPageRange(),
            'offset' => $this->getOffset(),
            'limit' => $this->getLimit()
        ];
    }

    /**
     * Gera HTML para navegação de páginas
     * @param string $baseUrl URL base para os links
     * @param string $pageParam Nome do parâmetro da página na URL
     * @return string
     */
    public function generateHtml($baseUrl = '', $pageParam = 'page')
    {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Page navigation"><ul class="pagination">';

        // Link para página anterior
        if ($this->hasPreviousPage()) {
            $prevUrl = $this->buildUrl($baseUrl, $pageParam, $this->getPreviousPage());
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">Anterior</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
        }

        // Links das páginas
        foreach ($this->getPageRange() as $page) {
            $pageUrl = $this->buildUrl($baseUrl, $pageParam, $page);
            $activeClass = $page == $this->currentPage ? ' active' : '';
            $html .= '<li class="page-item' . $activeClass . '"><a class="page-link" href="' . $pageUrl . '">' . $page . '</a></li>';
        }

        // Link para próxima página
        if ($this->hasNextPage()) {
            $nextUrl = $this->buildUrl($baseUrl, $pageParam, $this->getNextPage());
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Próxima</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Próxima</span></li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * Constrói a URL com o parâmetro da página
     * @param string $baseUrl
     * @param string $pageParam
     * @param int $page
     * @return string
     */
    private function buildUrl($baseUrl, $pageParam, $page)
    {
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        return $baseUrl . $separator . $pageParam . '=' . $page;
    }
}

