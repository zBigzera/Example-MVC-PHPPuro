<?php

namespace App\Core\Http\Middlewares;
use App\Core\Http\Request;
use App\Core\Http\Response;

class Api{
    /**
     * Método responsável por executaro middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

       //altera o content type para json
       $request->getRouter()->setContentType('application/json');
        
        return $next($request);
    }

}