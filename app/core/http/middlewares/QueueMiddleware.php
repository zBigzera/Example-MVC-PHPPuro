<?php

namespace App\Core\Http\Middlewares;
use App\Core\Http\Request;
use App\Core\Http\Response;

class QueueMiddleware {

    /**
     * Mapeamento de middlewares
     * @var array
     */
    private static $map = [];
    /**
     * Mapeamento de middlewares que serão carregados em todas as rotas
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares a serem executados
     * @var array
     */
    private $middlewares = [];
    /**
     * Função de execução do controlador

     */
    private $controller;
    /**
     * Argumentos da função do controlador
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Método responsável por construir a classe de fila de middlewaers
     * @param array $middlewares
     * @param \Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs){
        $this->middlewares = array_merge(self::$default,$middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Método responsável por definir o mapeamento de middlewares
     * @param array $map
     * @return void
     */
    public static function setMap($map){
        self::$map = $map;
    }

      /**
     * Método responsável por definir o mapeamento de middlewares padrões
     * @param array $map
     * @return void
     */
    public static function setDefault($default){
        self::$default = $default;
    }
    /**
     * Método responsável por executar o proximo nivel da fila de middlewares
     * @param Request $request
     * @return Response
     */
    public function next($request){
        //Verifica se a fila está vazia
        if (empty($this->middlewares)) {
                return call_user_func_array($this->controller, $this->controllerArgs);
            }

            //Middleware
            //Array_shift -> remove o primeiro elemento de um array e retorna esse valor
            $middleware = array_shift($this->middlewares);

            //verifica o mapeamento
            if(!isset(self::$map[$middleware])){
                throw new \Exception("Problemas ao processar o middleware da requisição",500);
            }

            //NEXT
            $queue = $this;
            $next = function($request) use ($queue){
                return $queue->next($request);
            };

            //Executa o middleware
            return (new self::$map[$middleware])->handle($request, $next);
            
    }
        

}