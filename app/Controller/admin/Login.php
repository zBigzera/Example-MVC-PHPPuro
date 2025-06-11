<?php

namespace App\Controller\Admin;
use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Http\Request;
class Login extends Page{
/**
 * Método responsável por retornar a renderização da págia de login.
 * @param Request $request
 * @param string $errorMessage
 * @return string
 */
    public static function getLogin($request, $errorMessage = null){
          $content = View::render('admin/login', [
        'mensagem' => $errorMessage
    ]);

        return parent::getPage('Login', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLogin($request){
        $postVars = $request->getPostVars();

        $email = $postVars['email'] ?? '';
        $senha = $postVars['password'] ?? '';

        //busca o usuário pelo e-mail
        $obUser = User::getUserByEmail($email);

        if(!$obUser instanceof User){
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }

        //verifica a senha do usuário
        if(!password_verify($senha, $obUser->senha)){
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }
        
    }

}