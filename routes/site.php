<?php

use \App\Core\Http\Response;
use \App\Controller\site;

$obRouter->get('/',[
    function(){
        return new Response(200, Site\Home::getHome());
    }
]);

$obRouter->get('/sobre',[
    'middlewares' => [
        'cache'
    ],
    function(){
        return new Response(200, Site\About::getAbout());
    }
]);

$obRouter->get('/depoimentos',[
    function($request){
        return new Response(200, Site\Testimony::getTestimonies($request));
    }
]);

// Rota de depoimento INSERT
$obRouter->post('/depoimentos',[
    'middlewares' => [
        'cache'
    ],
    function($request){
        return new Response(200, Site\Testimony::insertTestimony($request));
    }
]);



//Rota dinâmica
// $obRouter->get('/pagina/{idPagina}',[
//     function($idPagina){
//         return new Response(200, 'Página '.$idPagina);
//     }
// ]);
