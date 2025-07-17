<?php

use \App\Core\Http\Response;
use \App\Controller\site;

$obRouter->get('/', [
    function(){
        return new Response(200, Site\Home::getHome());
    }
]);

$obRouter->group(['cache'], function($obRouter) {
    $obRouter->get('/sobre', [
        function(){
            return new Response(200, Site\About::getAbout());
        }
    ]);
    
    $obRouter->post('/depoimentos', [
        function($request){
            return new Response(200, Site\Testimony::insertTestimony($request));
        }
    ]);
});

$obRouter->get('/depoimentos', [
    function($request){
        return new Response(200, Site\Testimony::getTestimonies($request));
    }
]);

//Rota dinâmica (comentada)
// $obRouter->get('/pagina/{idPagina}',[
//     function($idPagina){
//         return new Response(200, 'Página '.$idPagina);
//     }
// ]);
