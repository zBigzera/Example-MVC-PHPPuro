<?php

use \App\Http\Response;
use \App\Controller\Api;

//listar depoimentos
$obRouter->get('/api/v1/testimonies', [
    'middlewares' => [
        'api'
    ],

    function($request){
        return new Response (200, Api\Testimony::getTestimonies($request), 'application/json');
    }
]);

$obRouter->get('/api/v1/testimonies/{id}/', [
     'middlewares' => [
        'api'
    ],
    function($request, $id){
        return new Response (200, Api\Testimony::getTestimony($request, (int)$id), 'application/json');
    }
]);

$obRouter->post('/api/v1/testimonies/', [
     'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request){
        return new Response (201, Api\Testimony::setNewTestimony($request), 'application/json');
    }
]);


$obRouter->put('/api/v1/testimonies/{id}', [
     'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response (200, Api\Testimony::setEditTestimony($request, $id), 'application/json');
    }
]);


$obRouter->delete('/api/v1/testimonies/{id}', [
     'middlewares' => [
        'api',
        'user-basic-auth'
    ],
    function($request, $id){
        return new Response (200, Api\Testimony::setDeleteTestimony($request, $id), 'application/json');
    }
]);
