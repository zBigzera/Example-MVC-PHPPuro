<?php

namespace App\Core\Http\Middlewares;
use App\Core\Http\Request;
use App\Core\Http\Response;
use \App\Model\Entity\User;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth{


    /**
     * Método resposnável por retornar uma instância de usuário autenticado
     * @return mixed
     */
    private function getJWTAuthUser($request){
        //headers

        $headers = $request->getHeaders();
        
        //token puro
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try{
            $decoded = JWT::decode($jwt, new Key($_SERVER['JWT_KEY'], 'HS256'));
        }catch(\Exception $e){
            throw new \Exception("Token inválido",403);
        }
      

        $email = $decoded->email ?? '';

        $obUser = User::getUserByEmail($email);

        
        return $obUser instanceof User ? $obUser : false;
    }

    /**
     * Método responsável por validar o acesso via HTTP BASIC AUTH
     * @param \App\Core\Http\Request $request
     */
    private function Auth($request){
        //verifica o usuário recebido
        if($obUser = $this->getJWTAuthUser($request)){
            $request->user = $obUser;
            return true;
        }

        throw new \Exception("Acesso negado", 403);
    }
    /**
     * Método responsável por executaro middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        //Realiza a validação do acesso via jwt

        $this->auth($request);
        
        return $next($request);
    }

}