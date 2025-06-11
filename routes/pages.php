<?php

use \App\Http\Response;
use \App\Controller\Pages;

$obRouter->get('/',[
    function(){
        return new Response(200, Pages\Home::getHome());
    }
]);

$obRouter->get('/sobre',[
    function(){
        return new Response(200, Pages\About::getAbout());
    }
]);

$obRouter->get('/depoimentos',[
    function($request){
        return new Response(200, Pages\Testimony::getTestimonies($request));
    }
]);

// Rota de depoimento INSERT
$obRouter->post('/depoimentos',[
    function($request){
        return new Response(200, Pages\Testimony::insertTestimony($request));
    }
]);



//Rota dinâmica
// $obRouter->get('/pagina/{idPagina}',[
//     function($idPagina){
//         return new Response(200, 'Página '.$idPagina);
//     }
// ]);
