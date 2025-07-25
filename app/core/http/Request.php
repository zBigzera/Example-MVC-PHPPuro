<?php
namespace App\Core\Http;

if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $headers[str_replace('_', '-', substr($name, 5))] = $value;
            }
        }
        return $headers;
    }
}

use App\Core\Http\Router;
use App\Model\Entity\User as UserModel;
class Request
{

    /**
     * Método HTTP da requisição
     * @var string
     */
    private $httpMethod;

    /**
     * URI da página
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas via POST da página ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição
     * @var array
     */
    private $headers = [];
    /**
     * Instância do router
     * @var 
     */
    private Router $router;

    /**
     * Construtor da classe
     * Inicializa os dados da requisição (GET, POST, HEADERS, URI, método HTTP)
     */

    /**
     * Usuário autenticado
     * @var UserModel
     */
    public UserModel $user;

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();

    }

    /**
     * Método responsável por definir as variáveis do POST
     */
    private function setPostVars()
    {

        if ($this->httpMethod == 'GET')
            return false;

        //post padrao
        $this->postVars = $_POST ?? [];

        //post json

        $inputRaw = file_get_contents('php://input');

        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }


    /**
     * Método responsável por definir a URI
     * @return void
     */
    private function setUri()
    {
        //url completa (com gets)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }
    /**
     * Retorna o método HTTP da requisição
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Retorna a URI da requisição
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Retorna os parâmetros da URL ($_GET)
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Retorna os dados enviados via POST ($_POST)
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

    /**
     * Retorna os cabeçalhos da requisição
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }


    public function getRouter()
    {
        return $this->router;
    }

    public function getCurrentUrl(): string
    {
        return $this->router->getCurrentUrl();
    }

    public function getFullUrl(): string
    {
        $base = $this->getCurrentUrl();
        $query = http_build_query($this->queryParams);
        return $base . ($query ? '?' . $query : '');
    }


}