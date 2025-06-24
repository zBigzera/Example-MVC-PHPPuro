<?php

namespace App\Http;

class Response{

    /**
     * Código do status HTPP
     * @var int
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do response
     * @var array
     */
    private $headers = [];
    /**
     * Tipo de conteúdo que está sendo retornado
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do response
     * @var mixed
     */
    private $content;
    /**
     * Método responsável por iniciar a classe e definir os valores
     * @param integer $httpCode
     * @param mixed $content
     * @param string $contentType
     */
    public function __construct($httpCode, $content, $contentType = 'text/html'){
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
       

    }
    /**
     * Método responsável por alterar o content type do response
     * @param string $contentType
     * @return void
     */
    public function setContentType($contentType){
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }
    /**
     * Método responsável por adicionar um registro no cabeçalho do response
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function addHeader($key,$value){
        $this->headers[$key] = $value;
    }

    /**
     * Método responável por enviar os headers para o navegador
     * @return void
     */
    private function sendHeaders(){
        //Status
        http_response_code($this->httpCode);

        //enviar headers

        foreach($this->headers as $key=>$value){
            header($key.': '.$value);
        }
    }
    /**
     * Método responsável por enviar a responsta para o usuário
     * @return void
     */
    public function sendResponse(){
        //Envia os headers
        $this->sendHeaders();
        //Imprime o conteúdo
        switch($this->contentType){
            case 'text/html':{
                echo $this->content;
                break;
            }

            case 'application/json':{
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                break;
            }
        }
    }
}