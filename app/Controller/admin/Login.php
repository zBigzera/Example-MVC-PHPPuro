<?php

namespace App\Controller\Admin;
use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Http\Request;
Use \App\Session\Admin\Login as SessionAdminlogin;
Use \App\Controller\Admin\Alert;
class Login extends Page{
/**
 * Método responsável por retornar a renderização da págia de login.
 * @param Request $request
 * @param string $errorMessage
 * @return string
 */
    public static function getLogin($request, $errorMessage = null){


    $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';
    
        return View::render('admin/pages/login.twig', [
            'title' => 'Login',
            'aviso' => $status
        ]);

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

        //cria sa sessão de login
        SessionAdminlogin::login($obUser);

        //Redireciona para home/admin
        $request->getRouter()->redirect('/admin/');
    }

    /**
     * Método resposável por deslogar o usuário
     * @param Request $request
     * @return void
     */
    public static function setLogout($request){

        //Destrói a sessão de login
        SessionAdminLogin::logout();

        $request->getRouter()->redirect('/admin/login');
    }

}