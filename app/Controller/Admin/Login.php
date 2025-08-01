<?php

namespace App\Controller\Admin;
use App\Core\View;
use App\Model\Service\UserService;
use App\Model\DTO\UserDTO;

use App\Core\Http\Request;
Use App\Session\Admin\AdminLogin as SessionAdminlogin;
Use App\Controller\Admin\Alert;

class Login extends Page{

    private $userService;

    public function __construct(UserService $user)
    {
        $this->userService = $user;
    }


    /**
     * Método responsável por retornar a renderização da págia de login.
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public function getLogin($request, $errorMessage = null){

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
    public function setLogin($request){
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['password'] ?? '';


        $obUser = $this->userService->login($email, $senha);
      
        if(!$obUser){
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }

        SessionAdminlogin::login($obUser);
        $request->getRouter()->redirect('/admin/');
    }


    /**
     * Método resposável por deslogar o usuário
     * @param Request $request
     * @return void
     */
    public function setLogout($request){

        //Destrói a sessão de login
        SessionAdminLogin::logout();

        $request->getRouter()->redirect('/admin/login');
    }

}