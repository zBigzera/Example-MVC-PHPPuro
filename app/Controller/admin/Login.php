<?php

namespace App\Controller\Admin;
use App\Core\View;
use App\Model\Entity\User;
use App\Core\Http\Request;
Use App\Session\Admin\AdminLogin as SessionAdminlogin;
Use App\Controller\Admin\Alert;

class Login extends Page{

    private $userEntity;

    public function __construct(User $userEntity)
    {
        $this->userEntity = $userEntity;
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

        //busca o usuário pelo e-mail
        $obUser = $this->userEntity->getUserByEmail($email);

        if(!$obUser instanceof User){
            return self::getLogin($request, 'E-mail ou senha inválidos.');
        }

        //verifica a senha do usuário
        if(!password_verify($senha, $obUser->senha)){
            return $this->getLogin($request, 'E-mail ou senha inválidos.');
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
    public function setLogout($request){

        //Destrói a sessão de login
        SessionAdminLogin::logout();

        $request->getRouter()->redirect('/admin/login');
    }

}