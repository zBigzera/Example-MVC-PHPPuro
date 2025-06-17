<?php

namespace App\Http\Middleware;

use \App\Session\Admin\Login as SessionAdminlogin;
class RequireAdminLogout{

   /**
     * Método responsável por executar o middleware
     * @param \App\Http\Request $request
     * @param Closure $next
     * @return \App\Http\Response
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