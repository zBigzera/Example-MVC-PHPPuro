<?php

use \App\Core\Http\Response;
use \App\Controller\Admin;
$obRouter->get('/admin/testimonies',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Testimony::getTestimonies($request));
    }
]);

$obRouter->get('/admin/testimonies/new',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Testimony::getNewTestimony($request));
    }
]);

$obRouter->post('/admin/testimonies/new',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request){
        return new Response(200, Admin\Testimony::setNewTestimony($request));
    }
]);

$obRouter->get('/admin/testimonies/{id}/edit',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request, $id){
        return new Response(200, Admin\Testimony::getEditTestimony($request, $id));
    }
]);

$obRouter->post('/admin/testimonies/{id}/edit',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request, $id){
        return new Response(200, Admin\Testimony::setEditTestimony($request, $id));
    }
]);

$obRouter->get('/admin/testimonies/{id}/delete',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request, $id){
        return new Response(200, Admin\Testimony::getDeleteTestimony($request, $id));
    }
]);

$obRouter->post('/admin/testimonies/{id}/delete',[
     'middlewares' =>[
        'require-admin-login'
    ],
    function($request, $id){
        return new Response(200, Admin\Testimony::setDeleteTestimony($request, $id));
    }
]);