<?php

namespace App\Controller\Api;

use App\Model\Entity\User;

use Firebase\JWT\JWT;

class Auth extends Api{

    /**
     * Método responsável por gerar um token JWT
     * @param \App\Core\Http\Request $request
     * @return array
     */
    public static function generateToken($request){
        //post vars
        $postVars = $request->getPostVars();

        //valida os campos obrigatórios
        
        if(!isset($postVars['email']) || !isset($postVars['senha'])){
            throw new \Exception("Os campos 'email' e 'senha' são obrigatórios",400);
        }

        $obUser = User::getUserByEmail($postVars['email']);

        if(!$obUser instanceof User){
            throw new \Exception("Usuário ou senha inválidos",400);
        }


        if(!password_verify($postVars['senha'], $obUser->senha)){
            throw new \Exception("Usuário ou senha inválidos",400);
        }

        //payload
        $payload = [
            'email' => $obUser->email
        ];


        return[
            'token' => JWT::encode($payload, $_SERVER['JWT_KEY'], 'HS256'),
        ];
    }
}