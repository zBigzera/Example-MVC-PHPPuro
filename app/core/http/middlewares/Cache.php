<?php

namespace App\Core\Http\Middlewares;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\FileCache as CacheFile;

class Cache{


    /**
     * Método responsável por verificar se a request atual pode ser cacheada
     * @param \App\Core\Http\Request $request
     * @return boolean
     */
    private function isCacheable($request){
        //valida o tempo de cache

        if($_SERVER['CACHE_TIME'] <= 0){
            return false;
        }

        //valida o método da requisição
        if($request->getHttpMethod() != "GET"){
            return false;
        }

        //valida o header de cache
        $headers = $request->getHeaders();
        if(isset($headers['Cache-Control']) && $headers['Cache-Control'] == 'no-cache'){
            return false;
        }

        //cacheável
        return true;
    }

    /**
     * Método responsável por retornar a hash do cash
     * @param Request $request
     * @return string
     */
    private function getHash($request) {
        // Obtém a URI e normaliza removendo barra final e inicial
        $uri = rtrim($request->getRouter()->getUri(), '/');
        $uri = ltrim($uri, '/');

        // Query params
        $queryParams = $request->getQueryParams();
        $uri .= !empty($queryParams) ? '?' . http_build_query($queryParams) : '';

        // Remove caracteres especiais e retorna o hash
        return rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', $uri), '-');
    }



    /**
     * Método responsável por executaro middleware
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next){

        //verifica se a request atual é cacheável
        if(!$this->isCacheable($request)) return $next($request);
        

        //hash do cache
        $hash = $this->getHash($request);

        return CacheFile::getCache($hash, $_SERVER['CACHE_TIME'], function() use($request, $next){
           return $next($request);
        });
    }

}