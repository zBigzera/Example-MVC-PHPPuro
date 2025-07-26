<?php

namespace App\Core\Http;
use \Closure;
use \Exception;
use DI\Container;
use App\Core\Http\Middlewares\QueueMiddleware as MiddlewareQueue;

class Router
{

    /**
     * URL completa do projeto (raíz)
     * @var string
     */
    private $url = "";

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = "";

    /**
     * Índice de rotas
     * @var array
     */
    private $routes = [];

    /**
     * Instância de Request
     * @var Request
     */
    private $request;

    /**
     * Content type padrão do response
     * @var string
     */
    private $contentType = "text/html";

    /**
     * Instância do container PHP-DI
     * @var Container
     */
    private $container;

    /**
     * Middlewares de grupo ativos
     * @var array
     */
    private $groupMiddlewares = [];

    /**
     * Método responsável por inicial a classe
     * @param string $url
     * @param Container $container
     */
    public function __construct($url, Container $container)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->container = $container;
        $this->setPrefix();
    }

    /**
     * Método responsável por alterar o valor do content type
     * @param string $contentType
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Método responsável por definir o prefixo das rotas
     * @return void
     */
    private function setPrefix()
    {
        //Informações da url atual (scheme, host, path)
        $parseUrl = parse_url($this->url);

        //define prefixo
        $this->prefix = $parseUrl["path"] ?? "";
    }

    /**
     * Método responsável por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array|Closure $params
     * @return Route
     */
    private function addRoute($method, $route, $params = [])
    {
        // Se $params for um Closure, converte para o formato esperado
        if ($params instanceof Closure) {
            $params = ["controller" => $params];
        }

        // Garantir que $params seja um array
        if (!is_array($params)) {
            $params = [];
        }


        // Se não há controller definido
        if (!isset($params["controller"])) {
            // Se for um array tipo [Classe, método]
            if (is_array($params) && count($params) === 2 && is_string($params[0]) && is_string($params[1])) {
                $params = ["controller" => $params];
            } else {
                // Procura por Closure
                foreach ($params as $key => $value) {
                    if ($value instanceof Closure) {
                        $params["controller"] = $value;
                        unset($params[$key]);
                        break;
                    }
                }
            }
        }

        //Middlewares da rota (inclui middlewares de grupo)
        $params["middlewares"] = array_merge($this->groupMiddlewares, $params["middlewares"] ?? []);

        //Variáveis da rota
        $params["variables"] = [];

        //Padrão de validação das variáveis das rotas
        $patternVariable = "/{(.*?)}/";
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, "(.*?)", $route);
            $params["variables"] = $matches[1];
        }

        //Padrão de validação da URL
        $patternRoute = '/^' . str_replace('/', '\/', rtrim($route, '/')) . '\/?$/';



        //Adiciona a rota dentro da classe
        $this->routes[$patternRoute][$method] = $params;

        //Retorna uma instância de Route para permitir método fluente
        return new Route($this, $patternRoute, $method);
    }

    /**
     * Método responsável por adicionar middlewares a uma rota específica
     * @param string $pattern
     * @param string $method
     * @param array $middlewares
     * @return void
     */
    public function addMiddlewaresToRoute($pattern, $method, $middlewares)
    {
        if (isset($this->routes[$pattern][$method])) {
            $this->routes[$pattern][$method]["middlewares"] = array_merge(
                $this->routes[$pattern][$method]["middlewares"],
                is_array($middlewares) ? $middlewares : [$middlewares]
            );
        }
    }

    /**
     * Método responsável por agrupar rotas com middlewares comuns
     * @param array $middlewares
     * @param Closure $callback
     * @return void
     */
    public function group($middlewares, Closure $callback)
    {
        // Salva os middlewares de grupo atuais
        $previousGroupMiddlewares = $this->groupMiddlewares;

        // Adiciona os novos middlewares de grupo
        $this->groupMiddlewares = array_merge($this->groupMiddlewares, is_array($middlewares) ? $middlewares : [$middlewares]);

        // Executa o callback com os middlewares de grupo ativos
        $callback($this);

        // Restaura os middlewares de grupo anteriores
        $this->groupMiddlewares = $previousGroupMiddlewares;
    }

    /**
     * Método responsável por definitir uma rota de GET
     * @param string $route
     * @param mixed $params
     * @return Route
     */
    public function get($route, $params = [])
    {
        return $this->addRoute("GET", $route, $params);
    }

    /**
     * Método responsável por definitir uma rota de POST
     * @param string $route
     * @param mixed $params
     * @return Route
     */
    public function post($route, $params = [])
    {
        return $this->addRoute("POST", $route, $params);
    }

    /**
     * Método responsável por definitir uma rota de PUT
     * @param string $route
     * @param mixed $params
     * @return Route
     */
    public function put($route, $params = [])
    {
        return $this->addRoute("PUT", $route, $params);
    }

    /**
     * Método responsável por definitir uma rota de DELETE
     * @param string $route
     * @param mixed $params
     * @return Route
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute("DELETE", $route, $params);
    }

    /**
     * Método repsonsável por retornar a URI desconsiderando o prefixo
     * @return string
     */
    public function getUri()
    {
        $uri = $this->request->getUri();
        //Corta a URI
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //Retorna a URI sem prefixo
        return end($xUri);
    }

    /**
     * Método responsável por retornar os dados da rota atual
     * @return array
     */
    private function getRoute()
    {
        //URI
        $uri = $this->getUri();

        //Method
        $httpMethod = $this->request->getHttpMethod();

        //Validar as rotas
        foreach ($this->routes as $patternRoute => $methods) {

            if (preg_match($patternRoute, $uri, $matches)) {
                //verificar o método
                if (isset($methods[$httpMethod])) {
                    //Removo a primeira posição
                    unset($matches[0]);

                    //variáveis processadas
                    $keys = $methods[$httpMethod]["variables"];
                    $methods[$httpMethod]["variables"] = array_combine($keys, $matches);
                    $methods[$httpMethod]["variables"]["request"] = $this->request;

                    return $methods[$httpMethod];
                }

                throw new Exception("Método não permitido", 405);
            }
        }

        throw new Exception("URL não encontrada", 404);
    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run()
    {
        try {
            $route = $this->getRoute();

            if (!isset($route["controller"])) {
                throw new Exception("A URL não pôde ser processada", 500);
            }

            $controller = $route["controller"];
            $variables = $route["variables"] ?? [];

            // Para o MiddlewareQueue, passamos o controller e os argumentos
            // Mas vamos usar o container->call para resolver os argumentos e chamar no final da fila.

            // Criamos uma função anônima para passar para o MiddlewareQueue,
            // que quando executada chama o controller com DI e rota
            $callable = function () use ($controller, $variables) {
                $result = $this->container->call($controller, $variables);

                // Se já é uma Response, retorna direto
                if ($result instanceof Response) {
                    return $result;
                }

                // Senão, assume que é string ou array, e cria uma Response
                return new Response(200, $result, $this->contentType);
            };


            // A fila de middlewares recebe o controller e os argumentos (aqui só o callable)
            return (new MiddlewareQueue($route["middlewares"], $callable, [], $this->container))->next($this->request);

        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }


    /**
     * Método resposnável por retornar a mensagem de erro de acordo com o content Type
     * @param mixed $message
     * @return mixed
     */
    private function getErrorMessage($message)
    {
        switch ($this->contentType) {
            case "application/json": {
                return ["error" => $message];

            }
            default: {
                return $message;
            }
        }
    }

    /**
     * Método responsável por retornar a URL atual
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url . $this->getUri();
    }

    /**
     * Método responsável por redirecionar a URL
     * @param string $route
     */
    public function redirect($route)
    {
        //URL
        $url = $this->url . $route;

        //executa o redirect
        header("location: " . $url);
        exit;
    }

    public function getContainer()
    {
        return $this->container;
    }

}