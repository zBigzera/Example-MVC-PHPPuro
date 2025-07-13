<?php

namespace App\Core\Http\Middlewares;
use App\Core\Http\Request;
use App\Core\Http\Response;

class Maintenance{
    /**
     * Método responsável por executaro middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        //verifica o estado de manutenção da página
        if($_ENV['MAINTENANCE'] === 'true'){
            throw new \Exception("Página em manutenção. tente novamente mais tarde.", 200);
        }
        
        return $next($request);
    }

}