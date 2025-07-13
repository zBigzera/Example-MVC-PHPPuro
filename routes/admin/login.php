<?php

use \App\Core\Http\Response;
use \App\Controller\Admin;

$obRouter->get('/admin/login',[
    'middlewares' =>[
        'require-admin-logout'
    ],
    function($request){
        return new Response(200, Admin\Login::getLogin($request));
    }
]);


$obRouter->get('/admin/logout',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Login::setLogout($request));
    }
]);


$obRouter->post('/admin/login',[
     'middlewares' =>[
        'require-admin-logout'
    ],
    function($request){
        // print_r($request->getPostVars());
        return new Response(200, Admin\Login::setLogin($request));
    }
]);
