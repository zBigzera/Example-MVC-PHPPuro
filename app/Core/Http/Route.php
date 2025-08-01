<?php

namespace App\Core\Http;

class Route {
    
    /**
     * Instância do Router
     * @var Router
     */
    private $router;
    
    /**
     * Padrão da rota
     * @var string
     */
    private $pattern;
    
    /**
     * Método HTTP da rota
     * @var string
     */
    private $method;
    
    /**
     * Construtor da classe Route
     * @param Router $router
     * @param string $pattern
     * @param string $method
     */
    public function __construct($router, $pattern, $method) {
        $this->router = $router;
        $this->pattern = $pattern;
        $this->method = $method;
    }
    
    /**
     * Método responsável por adicionar middlewares à rota
     * @param array $middlewares
     * @return Route
     */
    public function middleware($middlewares) {
        $this->router->addMiddlewaresToRoute($this->pattern, $this->method, $middlewares);
        return $this;
    }
}

