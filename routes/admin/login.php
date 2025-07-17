<?php

use \App\Core\Http\Response;
use \App\Controller\Admin;
$obRouter->group(['require-admin-logout'], function($obRouter){

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
});


$obRouter->get('/admin/logout',[
    function($request){
        return new Response(200, Admin\Login::setLogout($request));
    }
])->middleware(['require-admin-login']);


