<?php

namespace App\Core\Http\Middlewares;

use \App\Session\Admin\AdminLogin as SessionAdminlogin;
class RequireAdminLogout{

   /**
     * Método responsável por executar o middleware
     * @param \App\Core\Http\Request $request
     * @param Closure $next
     * @return \App\Core\Http\Response
     */
    public function handle($request, $next){

        //verifica se o usuário está logado
        if(SessionAdminlogin::isLogged()){
            $request->getRouter()->redirect('/admin/');
        }

        //Continua a execução
        return $next($request);
        
    }

}