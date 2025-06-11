<?php

use \App\Http\Response;
use \App\Controller\Admin;

$obRouter->get('/admin',[
    function(){
        return new Response(200, 'Admin');
    }
]);

$obRouter->get('/admin/login',[
    function($request){
        return new Response(200, Admin\Login::getLogin($request));
    }
]);

$obRouter->post('/admin/login',[
    function($request){
        // print_r($request->getPostVars());
        return new Response(200, Admin\Login::setLogin($request));
    }
]);

