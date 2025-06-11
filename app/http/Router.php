<?php

namespace App\Http;
use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;
class Router{

    /**
     * URL completa do projeto (raíz)
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';
    /**
     * Índice de rotas
     * @var array
     */
    private $routes =[];
    /**
     * Instância de Request
     * @var Request
     */
    private $request;

    /**
     * Método responsável por inicial a classe
     * @param string $url
     */
    public function __construct($url){
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();

    }


    /**
     * Método responsável por definir o prefixo das rotas
     * @return void
     */
    private function setPrefix(){
        //Informações da url atual (scheme, host, path)
        $parseUrl = parse_url($this->url);

        //define prefixo
        $this->prefix = $parseUrl['path'] ?? '';
    }
    /**
     * Método responsável por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array $params
     * @return void
     */
    private function addRoute($method, $route, $params = []){
        //Validação dos parâmetros
        foreach($params as $key => $value){
            if($value instanceof Closure){
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }
        //Middlewares da rota
        $params['middlewares'] = $params['middlewares'] ?? [];
        //Variáveis da rota
        $params['variables'] = [];

        //Padrão de validação das variáveis das rotas
        $patternVariable = "/{(.*?)}/";
        if(preg_match_all($patternVariable,$route,$matches)){
            $route = preg_replace($patternVariable,'(.*?)',$route);
            $params['variables'] = $matches[1];
        }
        //Padrão de validação da URL
      $patternRoute = '/^' . str_replace('/', '\/', rtrim($route, '/')) . '\/?$' . '/';


       //ADiciona a rota dentro da classe
       $this->routes[$patternRoute][$method] = $params;
    }
    /**
     * Método responsável por definitir uma rota de GET
     * @param string $route
     * @param mixed $params
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }

      /**
     * Método responsável por definitir uma rota de POST
     * @param string $route
     * @param mixed $params
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }

      /**
     * Método responsável por definitir uma rota de PUT
     * @param string $route
     * @param mixed $params
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }

      /**
     * Método responsável por definitir uma rota de DELETE
     * @param string $route
     * @param mixed $params
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Método repsonsável por retornar a URI desconsiderando o prefixo
     * @return string
     */
    private function getUri(){
        $uri = $this->request->getUri();
        //Corta a URI
        $xUri = strlen($this->prefix) ? explode($this->prefix,$uri) : [$uri];

        //Retorna a URI sem prefixo
        return end($xUri);

    }
    /**
     * Método responsável por retornar os dados da rota atual
     * @return array
     */
    private function getRoute(){
        //URI
        $uri = $this->getUri();

        //Method
        $httpMethod = $this->request->getHttpMethod();

        //Validar as rotas

        foreach($this->routes as $patternRoute=>$methods){
            if(preg_match($patternRoute, $uri, $matches)){
                //verificar o método
                if(isset($methods[$httpMethod])){
                    //Removo a primeira posição
                    unset($matches[0]);

                    //variáveis processadas
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }

                throw new Exception("Método não permitido",405);
            }
        }

        throw new Exception("URL não encontrada", 404);

    }
    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run(){
        try{
            //Obtém a rota atual
            $route = $this->getRoute();

            //Verifica o controlador
            if(!isset($route['controller'])){
                throw new Exception("A URL não pôde ser processada",500);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach($reflection->getParameters() as $parameter){
               $name =  $parameter->getName();
               $args[$name] = $route['variables'][$name] ?? '';
            }
            //Retorna a execução da fila de middlewares
            return (new MiddlewareQueue($route['middlewares'],$route['controller'],$args))->next($this->request);
            
        }catch(Exception $e){
            return new Response($e->getCode(),$e->getMessage());
        }

    }

    /**
     * Método responsável por retornar a URL atual
     * @return string
     */
    public function getCurrentUrl(){
        return $this->url.$this->getUri();
    }
}