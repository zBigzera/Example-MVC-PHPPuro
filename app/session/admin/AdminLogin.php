<?php

namespace App\Session\Admin;

class AdminLogin{
    /**
     * Método responsável por iniciar a sessão
     * @return void
     */
    private static function init(){
        //Verifica se a sessão não esta ativa
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }
    /**
     * Método responsável por criar o login do usuário
     * @param \App\Model\Entity\User $obUser
     * @return boolean
     */
    public static function login($obUser){
        self::init();

    
        
        $_SESSION['admin']['usuario'] = [
            'id' => $obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];

        return true;
    }


    //Verifica se o usuário esta logado
    public static function isLogged(){

        self::init();

        return isset($_SESSION['admin']['usuario']['id']);

    }

     public static function logout(){
        self::init();

        unset($_SESSION['admin']['usuario']);

        return true;
    }

}