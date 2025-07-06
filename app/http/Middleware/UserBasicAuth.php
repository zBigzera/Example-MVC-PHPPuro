<?php

namespace App\Http\Middleware;
use App\Http\Request;
use App\Http\Response;
use \App\Model\Entity\User;

class UserBasicAuth{


    /**
     * Método resposnável por retornar uma instância de usuário autenticado
     * @return mixed
     */
    private function getBasicAuthUser(){
        //Verifica a existência dos dados de acesso
        if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
            return false;
        }

        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);

        //verifica a instância
        if(!$obUser instanceof User){
            return false;
        }


        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }

    /**
     * Método responsável por validar o acesso via HTTP BASIC AUTH
     * @param \App\Http\Request $request
     */
    private function basicAuth($request){
        //verifica o usuário recebido
        if($obUser = $this->getBasicAuthUser()){
            $request->user = $obUser;
            return true;
        }

        throw new \Exception("Usuário ou senha inválidos", 403);
    }
    /**
     * Método responsável por executaro middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        //Realiza a validação do acesso via basic auth

        $this->basicAuth($request);
        
        return $next($request);
    }

}